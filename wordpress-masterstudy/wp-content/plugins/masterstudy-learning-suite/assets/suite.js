(function () {
  'use strict';
  function element(tag, text) { var node = document.createElement(tag); if (text) node.textContent = text; return node; }
  function initialize(widget) {
    if (widget.dataset.ready) return; widget.dataset.ready = '1';
    var base = widget.dataset.base, lesson = widget.dataset.lesson;
    function request(route, data) {
      return fetch(base + route + '/' + lesson, {method: 'POST', credentials: 'same-origin', headers: {'Content-Type': 'application/json', 'X-WP-Nonce': widget.dataset.nonce}, body: JSON.stringify(data || {})})
        .then(function (r) { return r.json().then(function (value) { if (!r.ok) throw new Error(value.message || 'Request failed'); return value; }); });
    }
    var tutor = widget.querySelector('.mls-tutor');
    var learnerForm = widget.querySelector('.mls-learner-form');
    if (learnerForm) learnerForm.addEventListener('submit', function (event) {
      event.preventDefault(); var button = learnerForm.querySelector('button'), status = learnerForm.querySelector('.mls-form-status'); button.disabled = true;
      request('form', Object.fromEntries(new FormData(learnerForm))).then(function () { status.textContent = 'Saved.'; }).catch(function (error) { status.textContent = error.message; }).finally(function () { button.disabled = false; });
    });
    if (tutor) tutor.addEventListener('submit', function (event) {
      event.preventDefault(); var button = tutor.querySelector('button'), answer = widget.querySelector('.mls-tutor-answer');
      button.disabled = true; answer.textContent = 'Thinking...';
      request('tutor', {question: tutor.elements.question.value}).then(function (data) {
        answer.replaceChildren(element('strong', 'Suggested explanation'), element('p', data.answer));
        (data.sources || []).forEach(function (source, index) { var details = element('details'); details.open = index === 0; details.appendChild(element('summary', source.heading)); details.appendChild(element('p', source.excerpt)); answer.appendChild(details); });
      }).catch(function (error) { answer.textContent = error.message; }).finally(function () { button.disabled = false; });
    });
    widget.querySelectorAll('[data-rating]').forEach(function (button) {
      button.addEventListener('click', function () {
        var card = button.closest('.mls-review-card'), status = card.querySelector('.mls-card-status');
        button.disabled = true;
        request('cards', {card: Number(card.dataset.card), rating: button.dataset.rating}).then(function (data) { status.textContent = 'Next review: ' + new Date(data.due * 1000).toLocaleString(); }).catch(function (error) { status.textContent = error.message; }).finally(function () { button.disabled = false; });
      });
    });
    var start = widget.querySelector('.mls-start');
    if (start) start.addEventListener('click', function () {
      var status = widget.querySelector('.mls-assessment-status'); start.disabled = true; status.textContent = 'Opening assessment...';
      request('assessment').then(function (data) { showAttempt(data); }).catch(function (error) { status.textContent = error.message; }).finally(function () { start.disabled = false; });
    });
    if (start && Number(widget.dataset.attempt) > 0) {
      var assessmentPanel = start.closest('details'); if (assessmentPanel) assessmentPanel.open = true;
      fetch(base + 'attempt/' + lesson + '?attempt=' + encodeURIComponent(widget.dataset.attempt), {credentials:'same-origin', headers:{'X-WP-Nonce':widget.dataset.nonce}}).then(function (response) { return response.json().then(function (value) { if (!response.ok) throw new Error(value.message || 'Attempt unavailable'); return value; }); }).then(showAttempt).catch(function (error) { widget.querySelector('.mls-assessment-status').textContent = error.message; });
    }
    function showAttempt(data) {
      var status = widget.querySelector('.mls-assessment-status'), body = widget.querySelector('.mls-assessment-body');
      status.textContent = data.status.replaceAll('_', ' ') + (data.status === 'in_progress' && data.expires_at ? '. Deadline: ' + new Date(data.expires_at * 1000).toLocaleString() : '');
      body.replaceChildren();
      start.textContent = data.status === 'pending_review' ? 'View submitted assessment' : (data.status === 'in_progress' ? 'Continue assessment' : 'Start another attempt');
      function evidenceUpload() {
        var panel = element('details'); panel.appendChild(element('summary', 'Supporting evidence'));
        (data.evidence || []).forEach(function (item) { var link = element('a', item.name); link.href = item.url; panel.appendChild(element('p')); panel.lastChild.appendChild(link); });
        if (!['in_progress', 'pending_review', 'revision_required'].includes(data.status)) return panel;
        var upload = element('form'), label = element('label', 'Evidence file (PDF, PNG, JPEG or text, up to 10 MB)'), file = element('input'); file.type = 'file'; file.name = 'file'; file.accept = '.pdf,.png,.jpg,.jpeg,.txt'; file.required = true; label.appendChild(file); upload.appendChild(label);
        var descriptionLabel = element('label', 'Evidence description'), description = element('textarea'); description.name = 'description'; description.rows = 3; description.minLength = 10; description.maxLength = 2000; description.required = true; description.setAttribute('aria-label', 'Describe the evidence and skill demonstrated'); descriptionLabel.appendChild(description); upload.appendChild(descriptionLabel);
        var button = element('button', 'Upload evidence'), feedback = element('p'); feedback.setAttribute('role', 'status'); button.type = 'submit'; upload.append(button, feedback); panel.appendChild(upload);
        upload.addEventListener('submit', function (event) {
          event.preventDefault(); if (file.files[0].size > 10 * 1024 * 1024) { feedback.textContent = 'Maximum file size is 10 MB.'; return; }
          button.disabled = true; var payload = new FormData(upload); payload.set('attempt', data.attempt);
          fetch(base + 'evidence/' + lesson, {method:'POST', credentials:'same-origin', headers:{'X-WP-Nonce':widget.dataset.nonce}, body:payload}).then(function (response) { return response.json().then(function (value) { if (!response.ok) throw new Error(value.message || 'Upload failed'); return value; }); }).then(function (value) { feedback.textContent = 'Evidence recorded: ' + value.name; upload.reset(); }).catch(function (error) { feedback.textContent = error.message; }).finally(function () { button.disabled = false; });
        }); return panel;
      }
      if (data.status !== 'in_progress') {
        if (data.score !== null) body.appendChild(element('p', 'Score: ' + data.score + '%'));
        body.appendChild(element('p', data.feedback || 'Your submission is recorded.'));
        Object.values(data.suggested_answers || {}).filter(Boolean).forEach(function (answer) { var review = element('details'); review.appendChild(element('summary', 'Review answer')); review.appendChild(element('p', answer)); body.appendChild(review); });
        body.appendChild(evidenceUpload());
        return;
      }
      var form = element('form');
      data.bank.questions.forEach(function (question) {
        var field = element('fieldset'); field.appendChild(element('legend', question.prompt));
        if (question.type === 'mcq') question.options.forEach(function (text, index) { var label = element('label'), input = element('input'); input.type = 'radio'; input.name = question.id; input.value = index; input.required = true; label.appendChild(input); label.appendChild(element('span', text)); field.appendChild(label); });
        else { var input = element('textarea'); input.name = question.id; input.required = true; input.minLength = 10; input.maxLength = 10000; input.rows = 5; input.setAttribute('aria-label', question.prompt); field.appendChild(input); }
        form.appendChild(field);
      });
      var submit = element('button', 'Submit assessment'); submit.type = 'submit'; form.appendChild(submit); body.appendChild(form); body.appendChild(evidenceUpload());
      form.addEventListener('submit', function (event) {
        event.preventDefault(); var answers = {}, fields = new FormData(form); submit.disabled = true;
        data.bank.questions.forEach(function (question) { answers[question.id] = question.type === 'mcq' ? Number(fields.get(question.id)) : fields.get(question.id); });
        request('attempt', {attempt: data.attempt, answers: answers}).then(function (result) { showAttempt(result); }).catch(function (error) { status.textContent = error.message; submit.disabled = false; });
      });
    }
  }
  function scan(root) { if (root.matches && root.matches('.mls-tools')) initialize(root); if (root.querySelectorAll) root.querySelectorAll('.mls-tools').forEach(initialize); }
  document.addEventListener('DOMContentLoaded', function () { scan(document); new MutationObserver(function (records) { records.forEach(function (r) { r.addedNodes.forEach(function (n) { if (n.nodeType === 1) scan(n); }); }); }).observe(document.body, {childList:true,subtree:true}); });
}());
