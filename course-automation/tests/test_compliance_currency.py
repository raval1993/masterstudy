import json
import os
import sys
import tempfile
import unittest
from pathlib import Path

sys.path.insert(0, os.environ.get("COURSE_AUTOMATION_SRC", str(Path(__file__).resolve().parents[1] / "src")))
from course_automation.compliance import scan_training_updates


class CurrencyChecks(unittest.TestCase):
    def test_unknown_is_not_current(self):
        with tempfile.TemporaryDirectory(dir=Path(__file__).resolve().parent) as directory:
            root = Path(directory)
            registry = root / "registry.json"
            updates = root / "updates.json"
            registry.write_text(json.dumps({"courses": [{"unit_code": code} for code in ("MISS1", "EMPTY2", "CUR3", "OLD4", "FALSE5")]}), encoding="utf-8")
            updates.write_text(json.dumps([{"unit_code": "EMPTY2"}, {"unit_code": "CUR3", "status": "Current"}, {"unit_code": "OLD4", "status": "Superseded", "replacement_unit_code": "NEW4"}, {"unit_code": "FALSE5", "Superseded": "false"}]), encoding="utf-8")
            result = scan_training_updates(registry, updates, root / "report.json")
            rows = {row["unit_code"]: row for row in result["courses"]}
            for code in ("MISS1", "EMPTY2", "FALSE5"):
                self.assertEqual(rows[code]["compliance_status"], "not_checked")
                self.assertEqual(rows[code]["action"], "verify_official_record")
            self.assertEqual(rows["CUR3"]["compliance_status"], "checked_current")
            self.assertEqual(rows["OLD4"]["compliance_status"], "needs_compliance_review")
            self.assertEqual(result["not_checked"], 3)


if __name__ == "__main__":
    unittest.main()
