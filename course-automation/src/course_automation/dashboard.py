from __future__ import annotations

import argparse
import csv
import html
import json
from http.server import BaseHTTPRequestHandler, ThreadingHTTPServer
from pathlib import Path
from urllib.parse import urlparse

from .settings import load_settings


CSS = """
:root {
  color-scheme: light;
  --bg: #f6f7f9;
  --ink: #1f2933;
  --muted: #667085;
  --line: #d8dee8;
  --panel: #ffffff;
  --accent: #0f766e;
  --warn: #a16207;
}
* { box-sizing: border-box; }
body {
  margin: 0;
  font-family: Arial, Helvetica, sans-serif;
  background: var(--bg);
  color: var(--ink);
}
header {
  padding: 24px 32px 16px;
  background: var(--panel);
  border-bottom: 1px solid var(--line);
}
h1 {
  margin: 0 0 8px;
  font-size: 24px;
  line-height: 1.2;
}
.subtitle {
  margin: 0;
  color: var(--muted);
}
main {
  padding: 24px 32px 40px;
}
.stats {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
  gap: 12px;
  margin-bottom: 20px;
}
.stat {
  background: var(--panel);
  border: 1px solid var(--line);
  border-radius: 8px;
  padding: 16px;
}
.stat b {
  display: block;
  font-size: 24px;
  margin-bottom: 4px;
}
.stat span {
  color: var(--muted);
}
table {
  width: 100%;
  border-collapse: collapse;
  background: var(--panel);
  border: 1px solid var(--line);
}
th, td {
  padding: 10px 12px;
  border-bottom: 1px solid var(--line);
  text-align: left;
  vertical-align: top;
  font-size: 14px;
}
th {
  background: #eef2f6;
  font-size: 12px;
  text-transform: uppercase;
  letter-spacing: .04em;
}
.status {
  display: inline-block;
  padding: 3px 8px;
  border-radius: 999px;
  background: #e6f4f1;
  color: var(--accent);
  font-weight: 700;
  font-size: 12px;
}
.pending {
  background: #fff7df;
  color: var(--warn);
}
.empty {
  background: var(--panel);
  border: 1px solid var(--line);
  border-radius: 8px;
  padding: 20px;
}
"""


class DashboardHandler(BaseHTTPRequestHandler):
    settings = load_settings()

    def do_GET(self) -> None:
        parsed = urlparse(self.path)
        if parsed.path == "/api/courses":
            self.respond_json(self.read_rows())
            return
        if parsed.path == "/api/summary":
            self.respond_json(self.read_summary())
            return
        self.respond_html(self.render_page())

    def log_message(self, format: str, *args: object) -> None:
        print("%s - %s" % (self.address_string(), format % args))

    def read_rows(self) -> list[dict[str, str]]:
        if not self.settings.tracker_path.exists():
            return []
        with self.settings.tracker_path.open("r", encoding="utf-8-sig", newline="") as handle:
            return list(csv.DictReader(handle))

    def read_summary(self) -> dict[str, object]:
        if not self.settings.summary_path.exists():
            return {}
        return json.loads(self.settings.summary_path.read_text(encoding="utf-8"))

    def respond_json(self, payload: object) -> None:
        body = json.dumps(payload, ensure_ascii=False, indent=2).encode("utf-8")
        self.send_response(200)
        self.send_header("Content-Type", "application/json; charset=utf-8")
        self.send_header("Content-Length", str(len(body)))
        self.end_headers()
        self.wfile.write(body)

    def respond_html(self, content: str) -> None:
        body = content.encode("utf-8")
        self.send_response(200)
        self.send_header("Content-Type", "text/html; charset=utf-8")
        self.send_header("Content-Length", str(len(body)))
        self.end_headers()
        self.wfile.write(body)

    def render_page(self) -> str:
        rows = self.read_rows()
        summary = self.read_summary()
        total = len(rows)
        extracted = sum(1 for row in rows if row.get("Extracted") == "yes")
        pending_video = sum(1 for row in rows if row.get("Video Generated") == "pending")
        pending_lms = sum(1 for row in rows if row.get("MasterStudy Added") == "pending")
        total_words = summary.get("total_words", 0)

        if rows:
            table_rows = "\n".join(self.render_row(row) for row in rows)
            table = f"""
            <table>
              <thead>
                <tr>
                  <th>Course ID</th>
                  <th>Course Name</th>
                  <th>Words</th>
                  <th>Sections</th>
                  <th>Source</th>
                  <th>Content</th>
                  <th>Video</th>
                  <th>MasterStudy</th>
                  <th>QA</th>
                </tr>
              </thead>
              <tbody>{table_rows}</tbody>
            </table>
            """
        else:
            table = '<div class="empty">No tracker data yet. Run the ingest command first.</div>'

        return f"""<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Course Automation Dashboard</title>
  <style>{CSS}</style>
</head>
<body>
  <header>
    <h1>Course Automation Dashboard</h1>
    <p class="subtitle">Information Technology sample ingestion and production tracker</p>
  </header>
  <main>
    <section class="stats">
      {self.stat(total, "Total courses")}
      {self.stat(extracted, "Extracted")}
      {self.stat(total_words, "Source words")}
      {self.stat(pending_video, "Pending video")}
      {self.stat(pending_lms, "Pending MasterStudy")}
    </section>
    {table}
  </main>
</body>
</html>"""

    def stat(self, value: object, label: str) -> str:
        return f'<div class="stat"><b>{html.escape(str(value))}</b><span>{html.escape(label)}</span></div>'

    def render_row(self, row: dict[str, str]) -> str:
        def cell(name: str) -> str:
            return html.escape(row.get(name, ""))

        return f"""
        <tr>
          <td><strong>{cell("Course ID")}</strong></td>
          <td>{cell("Course Name")}</td>
          <td>{cell("Word Count")}</td>
          <td>{cell("Section Count")}</td>
          <td><span class="status">{cell("Source Status")}</span></td>
          <td><span class="status pending">{cell("Content Updated")}</span></td>
          <td><span class="status pending">{cell("Video Generated")}</span></td>
          <td><span class="status pending">{cell("MasterStudy Added")}</span></td>
          <td><span class="status pending">{cell("QA Status")}</span></td>
        </tr>
        """


def run_server(host: str, port: int, data_dir: Path | None = None) -> None:
    if data_dir is not None:
        DashboardHandler.settings = load_settings(data_dir.parent if data_dir.name == "data" else None)
    server = ThreadingHTTPServer((host, port), DashboardHandler)
    print(f"Dashboard running at http://{host}:{port}")
    server.serve_forever()


def build_parser() -> argparse.ArgumentParser:
    parser = argparse.ArgumentParser(description="Run the course automation dashboard")
    parser.add_argument("--host", default="127.0.0.1")
    parser.add_argument("--port", type=int, default=8080)
    return parser


def main() -> int:
    args = build_parser().parse_args()
    run_server(args.host, args.port)
    return 0


if __name__ == "__main__":
    raise SystemExit(main())
