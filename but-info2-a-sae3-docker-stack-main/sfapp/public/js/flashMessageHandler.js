document.onload = () => {
    let flashMessages = document.querySelectorAll('.flash-message');
    flashMessages.forEach(e => {
        setTimeout(() => {
            delete e;
        }, 5000)
    });
}