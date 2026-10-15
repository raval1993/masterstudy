#!/usr/bin/env bash
set -euo pipefail
install -m 0644 /root/migration/authoring.service /etc/systemd/system/masterstudy-ai-authoring.service
install -m 0644 /root/migration/authoring.timer /etc/systemd/system/masterstudy-ai-authoring.timer
systemd-analyze verify /etc/systemd/system/masterstudy-ai-authoring.service /etc/systemd/system/masterstudy-ai-authoring.timer
systemctl daemon-reload
systemctl enable --now masterstudy-ai-authoring.timer
systemctl start masterstudy-ai-authoring.service
journalctl -u masterstudy-ai-authoring --no-pager -n 8
systemctl list-timers masterstudy-ai-authoring.timer --no-pager
