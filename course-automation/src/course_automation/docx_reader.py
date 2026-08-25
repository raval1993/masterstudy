from __future__ import annotations

import re
from dataclasses import dataclass, field
from pathlib import Path
from typing import Iterable

from docx import Document
from docx.document import Document as DocxDocument
from docx.oxml.table import CT_Tbl
from docx.oxml.text.paragraph import CT_P
from docx.table import Table
from docx.text.paragraph import Paragraph


HEADING_RE = re.compile(r"^(?P<num>\d+(?:\.\d+)*)\s+(?:[-\u2013]\s*)?(?P<title>.+)")
TOC_ENTRY_RE = re.compile(r".+\t\d+$")


@dataclass
class Block:
    kind: str
    text: str
    style: str = ""


@dataclass
class Section:
    heading: str
    level: int
    body: list[str] = field(default_factory=list)

    @property
    def word_count(self) -> int:
        return count_words("\n".join(self.body))


@dataclass
class ExtractedCourse:
    course_id: str
    title: str
    source_file: str
    category: str
    blocks: list[Block]
    sections: list[Section]

    @property
    def full_text(self) -> str:
        return "\n\n".join(block.text for block in self.blocks if block.text)

    @property
    def word_count(self) -> int:
        return count_words(self.full_text)

    @property
    def paragraph_count(self) -> int:
        return sum(1 for block in self.blocks if block.kind == "paragraph")

    @property
    def table_count(self) -> int:
        return sum(1 for block in self.blocks if block.kind == "table")


def count_words(text: str) -> int:
    return len(re.findall(r"\b[\w'-]+\b", text))


def parse_course_identity(path: Path) -> tuple[str, str]:
    stem = path.stem
    match = re.match(r"(?P<id>[A-Z]{2,}[A-Z0-9]*\d[A-Z0-9]*)\s+##\s+-\s+CPD\s+-\s+(?P<title>.+)", stem)
    if match:
        return match.group("id"), match.group("title").strip()
    parts = stem.split(" ", 1)
    course_id = parts[0].strip()
    title = parts[1].strip(" -") if len(parts) > 1 else stem
    return course_id, title


def iter_document_blocks(document: DocxDocument) -> Iterable[Block]:
    for child in document.element.body.iterchildren():
        if isinstance(child, CT_P):
            paragraph = Paragraph(child, document)
            text = normalize_text(paragraph.text)
            if text:
                yield Block(kind="paragraph", text=text, style=paragraph.style.name if paragraph.style else "")
        elif isinstance(child, CT_Tbl):
            table = Table(child, document)
            text = table_to_markdown(table)
            if text:
                yield Block(kind="table", text=text, style="Table")


def normalize_text(text: str) -> str:
    return re.sub(r"[ \u00a0]+", " ", text.replace("\r", "\n")).strip()


def table_to_markdown(table: Table) -> str:
    rows: list[list[str]] = []
    for row in table.rows:
        cells = [normalize_text(cell.text).replace("\n", " ") for cell in row.cells]
        if any(cells):
            rows.append(cells)
    if not rows:
        return ""

    width = max(len(row) for row in rows)
    padded = [row + [""] * (width - len(row)) for row in rows]
    header = padded[0]
    separator = ["---"] * width
    body = padded[1:]
    lines = [
        "| " + " | ".join(header) + " |",
        "| " + " | ".join(separator) + " |",
    ]
    lines.extend("| " + " | ".join(row) + " |" for row in body)
    return "\n".join(lines)


def strip_table_of_contents(blocks: list[Block]) -> list[Block]:
    if not blocks:
        return blocks

    objective_indices = [
        index
        for index, block in enumerate(blocks[:120])
        if block.text.strip().lower() == "objectives"
    ]
    if objective_indices:
        return blocks[objective_indices[-1] :]

    start_index = 0
    for index, block in enumerate(blocks[:80]):
        if block.text.lower() == "objectives":
            start_index = index
            break
        if index > 10 and not TOC_ENTRY_RE.match(block.text):
            start_index = index
            break
    return blocks[start_index:]


def detect_level(text: str, style: str) -> int | None:
    style_match = re.search(r"heading\s*(\d+)", style, re.IGNORECASE)
    if style_match:
        return int(style_match.group(1))

    match = HEADING_RE.match(text)
    if not match:
        if text.lower() == "objectives":
            return 1
        return None

    number = match.group("num")
    if "." in number:
        return len(number.split("."))
    return 1


def build_sections(blocks: list[Block]) -> list[Section]:
    sections: list[Section] = []
    current: Section | None = None

    for block in blocks:
        level = detect_level(block.text, block.style)
        if block.kind == "paragraph" and level is not None and len(block.text) <= 180:
            current = Section(heading=block.text, level=level)
            sections.append(current)
            continue

        if current is None:
            current = Section(heading="Source Material", level=1)
            sections.append(current)
        current.body.append(block.text)

    return sections


def read_docx_course(path: Path, category: str) -> ExtractedCourse:
    document = Document(path)
    course_id, title = parse_course_identity(path)
    blocks = strip_table_of_contents(list(iter_document_blocks(document)))
    sections = build_sections(blocks)
    return ExtractedCourse(
        course_id=course_id,
        title=title,
        source_file=str(path.resolve()),
        category=category,
        blocks=blocks,
        sections=sections,
    )
