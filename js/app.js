$(document).foundation();


$(document).ready (function () {

    /** @var body DOM */
    var body = $('body');
    
    
    /** @var form DOM */
    var form = body.find ('form');
    
    
    /** @var result DOM */
    var result = body.find ('#result');
    
    
    // Create a new corpus
    form.find ('.create-corpus-action').change (function (e) {
        
        /** @var input DOM Get myself */
        var input = $(this)[0];
        
        
        /** @var file File */
        var file = input.files[0];
        
        
        /** @var reader FileReader */
        var reader = new FileReader ();
        reader.readAsDataURL (file, "UTF-8");
        reader.onload = function () {
            
            /** @var content String */
            var content = reader.result;
            
            
            /** @var name String */
            var filename = file.name;
            
            
            // Create new corpus
            $.ajax ({
                method: 'post',
                dataType: 'json',
                data: {
                    filename: filename,
                    content: content
                },
                url: 'create-corpus',
                success: function (response) {
                    window.location.reload ();
                }
            });
            
            
        };
        
    });
    
    
    // Submit
    form.submit (function (e) {
    
        // Stop submit
        e.stopPropagation ();
    
    
        // Loading
        body.addClass ('loading-state');
    
    
        $.ajax ({
            method: 'get',
            dataType: 'json',
            data: {
                corpus: form.find ('[name="corpus"]').val (),
                word: form.find ('[name="word"]').val ()
            },
            url: 'distance',
            success: function (response) {
            
                var html = '';
            
                body.removeClass ('loading-state');
            
                $.each (response.rows, function (index, row) {
                    
                    var word = row[0];
                    var affiniy = row[1] * 100;
                    
                    html += '<div>' + word + ' <progress max="100" value="' + affiniy + '"></progress></div> ';
                });
            
                result.html (html);
            }
        });
        
    
        // Prevent submit
        return false;
    
    });

});
