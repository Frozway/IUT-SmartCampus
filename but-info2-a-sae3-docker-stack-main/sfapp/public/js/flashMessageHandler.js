document.onload = () => {
    let flashMessages = document.querySelectorAll('.flash-message');
    flashMessages.forEach(e => {
        setTimeout(() => {
            console.log("hehehea")
            delete e;
        }, 5000);
    });
}