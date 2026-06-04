(function(){
  const button = document.querySelector('[data-snxworks-copy-report]');
  if (!button) return;
  button.addEventListener('click', async function(){
    const textarea = document.getElementById('snxworks-chc-report');
    const status = document.querySelector('.snxworks-chc-copy-status');
    if (!textarea) return;
    try {
      await navigator.clipboard.writeText(textarea.value);
      if (status) status.textContent = 'Copied.';
    } catch (error) {
      textarea.focus();
      textarea.select();
      document.execCommand('copy');
      if (status) status.textContent = 'Copied.';
    }
  });
})();
