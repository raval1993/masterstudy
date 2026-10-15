(function () {
  'use strict';

  function init(widget) {
    if (widget.dataset.studyReady) return;
    widget.dataset.studyReady = 'true';
    var form = widget.querySelector('.mce-study-form');
    var status = widget.querySelector('.mce-save-status');
    var quiz = widget.querySelector('.mce-quiz');
    var member = widget.dataset.member === '1';
    var loaded = !member;
    var save = form.querySelector('button[type="submit"]');
    save.disabled = true;

    function request(method, data) {
      return fetch(widget.dataset.endpoint, {
        method: method, credentials: 'same-origin',
        headers: { 'X-WP-Nonce': widget.dataset.nonce, 'Content-Type': 'application/json' },
        body: data ? JSON.stringify(data) : undefined
      }).then(function (response) {
        if (!response.ok) throw new Error('Study record unavailable');
        return response.json();
      });
    }

    if (member) {
      form.querySelectorAll('textarea, input').forEach(function (input) { input.disabled = true; });
      status.textContent = 'Loading your study record...';
      request('GET').then(function (record) {
        form.elements.notes.value = record.notes || '';
        form.elements.log.value = record.log || '';
        form.elements.acknowledged.checked = record.acknowledged === true;
        form.querySelectorAll('input[name="checks"]').forEach(function (input) {
          input.checked = Array.isArray(record.checks) && record.checks.indexOf(Number(input.value)) !== -1;
        });
        loaded = true;
        save.disabled = false;
        form.querySelectorAll('textarea, input').forEach(function (input) { input.disabled = false; });
        status.textContent = record.saved_at ? 'Saved ' + new Date(record.saved_at).toLocaleString() : 'Your notes are private to your account.';
      }).catch(function () { status.textContent = 'Could not load your study record. Reload the lesson to retry.'; });
    }

    form.addEventListener('submit', function (event) {
      event.preventDefault();
      if (!member || !loaded || save.disabled) return;
      save.disabled = true;
      status.textContent = 'Saving...';
      var checks = Array.from(form.querySelectorAll('input[name="checks"]:checked')).map(function (input) { return Number(input.value); });
      request('POST', { notes: form.elements.notes.value, log: form.elements.log.value, checks: checks, acknowledged: form.elements.acknowledged.checked })
        .then(function (record) { status.textContent = 'Saved ' + new Date(record.saved_at).toLocaleString(); })
        .catch(function () { status.textContent = 'Could not save. Your text is still here; please try again.'; })
        .finally(function () { save.disabled = false; });
    });

    if (quiz) quiz.addEventListener('submit', function (event) {
      event.preventDefault();
      var answer = quiz.querySelector('input[type="radio"]:checked');
      if (!answer) return;
      var correct = answer.value === quiz.dataset.correct;
      var feedback = quiz.querySelector('.mce-feedback');
      feedback.textContent = (correct ? 'Correct. ' : 'Review this choice. ') + quiz.dataset.feedback;
      feedback.dataset.result = correct ? 'correct' : 'review';
    });

    form.querySelector('.mce-download').addEventListener('click', function () {
      var lesson = widget.closest('.masterstudy-course-player-lesson');
      var titleNode = document.querySelector('h1');
      var title = titleNode ? titleNode.textContent.trim() : 'Lesson study worksheet';
      var text = title + '\n\n';
      if (lesson) {
        var copy = lesson.cloneNode(true);
        copy.querySelectorAll('.mce-tools, video, script, style').forEach(function (node) { node.remove(); });
        text += Array.from(copy.querySelectorAll('h2,h3,h4,h5,h6,p,li')).filter(function (node) {
          return !node.parentElement.closest('li');
        }).map(function (node) { return node.textContent.trim(); }).filter(Boolean).join('\n\n');
      }
      text += '\n\nMY STUDY NOTES\n' + form.elements.notes.value + '\n\nLEARNING LOG\n' + form.elements.log.value;
      var blob = URL.createObjectURL(new Blob([text], { type: 'text/plain;charset=utf-8' }));
      var link = document.createElement('a');
      link.href = blob; link.download = 'lesson-study-worksheet.txt';
      link.click();
      setTimeout(function () { URL.revokeObjectURL(blob); }, 1000);
    });
  }

  function scan(root) {
    if (root.matches && root.matches('.mce-study')) init(root);
    if (root.querySelectorAll) root.querySelectorAll('.mce-study').forEach(init);
  }
  document.addEventListener('DOMContentLoaded', function () {
    scan(document);
    new MutationObserver(function (records) {
      records.forEach(function (record) { record.addedNodes.forEach(function (node) { if (node.nodeType === 1) scan(node); }); });
    }).observe(document.body, { childList: true, subtree: true });
  });
}());
