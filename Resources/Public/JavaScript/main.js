// all buttons should only be clicked once (per page)
// this is mainly here to prevent bigger actions (like an import or a cleanup task) from being triggered twice
document.addEventListener('DOMContentLoaded', (ev) => {
  (function () {
    function handleButton(button) {
        let clicked = false;
        button.addEventListener('click', function (e) {
            if (clicked) {
                e.preventDefault();
            } else {
                button.disabled = true;
                button.classList.add('disabled');
                clicked = true;
            }
        });
    }
    let buttons = document.getElementById('digitalmarketingframework-be-content').querySelectorAll('.btn');
    buttons.forEach(button => {
        handleButton(button);
    });
  })();
});
