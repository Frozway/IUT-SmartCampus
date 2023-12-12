$(document).ready(function() {
    // Cibler les messages Flash avec la classe flash-message
    $('.flash-message').each(function() {
        var message = $(this);

        // Masquer le message après 5 secondes (5000 millisecondes)
        setTimeout(function() {
            message.fadeOut();
        }, 5000);
    });
});