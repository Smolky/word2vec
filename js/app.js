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
    
    
    // Create a new model
    form.find ('.create-model-action').change (function (e) {
        
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
                url: 'create-model',
                success: function (response) {
                    window.location.reload ();
                }
            });
        };
    });

    
    // Generate a new model
    form.find ('.generate-model-action').click (function (e) {
    
        /** @var corpus String */
        var corpus = form.find ('[name="corpus"]').val ();
        if ( ! corpus) {
            return;
        }
        
        
        /** @var size int */
        var size = form.find ('[name="size"]').val () * 1;
        
        
        /** @var window_size int */
        var window_size = form.find ('[name="window_size"]').val () * 1;
        

        /** @var iterations int */
        var iterations = form.find ('[name="iterations"]').val () * 1;
        
        
        // Loading
        body.addClass ('loading-state');        
        
    
        // Generate a new model from the corpus
        $.ajax ({
            method: 'post',
            dataType: 'json',
            data: {
                corpus: corpus,
                size: size,
                window_size: window_size,
                iterations: iterations
            },
            url: 'create-model-from-corpus',
            success: function (response) {
                body.removeClass ('loading-state');                
                window.location.reload ();
            }
        });
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
                model: form.find ('[name="model"]').val (),
                word: form.find ('[name="word"]').val ()
            },
            url: 'distance',
            success: function (response) {
            
                var html = '';
            
                body.removeClass ('loading-state');
            
                $.each (response.rows, function (index, row) {
                    
                    var word = row[0];
                    var affinity = row[1] * 100;
                    
                    html += 
                        '<div>' 
                        + word 
                        + ' <progress max="100" value="' 
                        + affinity + '"></progress> ' 
                        + affinity.toFixed (8) 
                        + '%</div> '
                    ;
                });
            
                result.html (html);
            }
        });
        
    
        // Prevent submit
        return false;
    
    });
    
    
    // View corpus
    form.find ('.view-corpus-action').click (function (e) {
        
        /** @var corpus String */
        var corpus = form.find ('[name="corpus"]').val ();
        if ( ! corpus) {
            return;
        }
        
        
        window.open ('assets/corpus/' + corpus, '_blank');
        
    });
    
    
    // View clean corpus
    form.find ('.view-clean-corpus-action').click (function (e) {
        
        /** @var corpus String */
        var corpus = form.find ('[name="corpus"]').val ();
        if ( ! corpus) {
            return;
        }
        
        
        window.open ('tmp/corpus/' + corpus, '_blank');
        
    });

});
