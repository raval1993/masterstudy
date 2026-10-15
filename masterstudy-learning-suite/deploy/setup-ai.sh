#!/usr/bin/env bash
set -euo pipefail
export HOME=/root
base=/opt/masterstudy/ollama
install -d -m 0755 "$base"
if ! test -x "$base/bin/ollama"; then
  curl --fail --location --retry 2 --output /root/migration/ollama-latest.json https://api.github.com/repos/ollama/ollama/releases/latest
  python3 - <<'PY'
import hashlib, json, pathlib, urllib.request
release=json.loads(pathlib.Path('/root/migration/ollama-latest.json').read_text())
asset=next(a for a in release['assets'] if a['name']=='ollama-linux-amd64.tar.zst')
url=asset['browser_download_url']
if not url.startswith('https://github.com/ollama/ollama/releases/download/'):
    raise SystemExit('Unexpected AI download origin')
target=pathlib.Path('/root/migration/ollama-linux-amd64.tar.zst')
urllib.request.urlretrieve(url, target)
with target.open('rb') as stream:
    checksum=hashlib.file_digest(stream, 'sha256').hexdigest()
expected=asset.get('digest')
if expected and expected!='sha256:'+checksum:
    raise SystemExit('AI archive checksum mismatch')
print(json.dumps({'tag':release['tag_name'],'sha256':checksum,'source':url}), flush=True)
PY
  command -v zstd >/dev/null || { apt-get update -qq; apt-get install -y zstd; }
  tar --zstd -xf /root/migration/ollama-linux-amd64.tar.zst -C "$base"
fi
id masterstudy-ai >/dev/null 2>&1 || useradd --system --user-group --home-dir /opt/masterstudy/ai-models --shell /usr/sbin/nologin masterstudy-ai
install -d -m 0750 -o masterstudy-ai -g masterstudy-ai /opt/masterstudy/ai-models
install -m 0644 /root/migration/ollama.service /etc/systemd/system/masterstudy-local-ai.service
systemctl daemon-reload
systemctl enable --now masterstudy-local-ai.service
for attempt in $(seq 1 10); do
  if curl --fail --silent http://127.0.0.1:11435/api/version; then break; fi
  sleep 1
done
HOME=/opt/masterstudy/ai-models OLLAMA_MODELS=/opt/masterstudy/ai-models OLLAMA_HOST=127.0.0.1:11435 runuser -u masterstudy-ai --preserve-environment -- "$base/bin/ollama" pull qwen3:0.6b
systemctl is-active masterstudy-local-ai.service
