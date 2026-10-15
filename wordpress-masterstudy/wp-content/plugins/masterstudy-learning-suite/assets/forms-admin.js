(function () {
  'use strict';
  var form = document.getElementById('mls-form-builder');
  if (!form) return;
  var body = form.querySelector('tbody'), template = document.getElementById('mls-field-template'), counter = body.children.length;
  function refresh(row) {
    var type = row.querySelector('.mls-field-type').value;
    row.querySelector('.mls-choice-settings').hidden = type !== 'select';
    row.querySelector('.mls-number-settings').hidden = type !== 'number';
  }
  body.querySelectorAll('.mls-form-row').forEach(refresh);
  form.addEventListener('change', function (event) { if (event.target.matches('.mls-field-type')) refresh(event.target.closest('tr')); });
  form.addEventListener('click', function (event) {
    var remove = event.target.closest('.mls-remove-field');
    if (remove) { remove.closest('tr').remove(); return; }
    if (!event.target.closest('#mls-add-field')) return;
    if (body.children.length >= 15) { document.getElementById('mls-builder-status').textContent = 'Maximum 15 fields.'; return; }
    var fragment = template.content.cloneNode(true), row = fragment.querySelector('tr');
    row.querySelectorAll('[name]').forEach(function (input) { input.name = input.name.replace('__index__', counter); });
    counter += 1; refresh(row); body.appendChild(fragment); row.querySelector('input').focus();
  });
}());
