$(document).ready(() => {
    $('#form').on(
        'submit',
        function (e) {
            e.preventDefault();

            $.ajax({
                url: "send.php",
                method: 'POST',
                data: $(this).serialize(),
                dataType: 'text'
            }).done(function(request) {
                console.log('request is done', request);
            }).fail(function(err) {
                console.log("error", err);
            });
        }
    );
});