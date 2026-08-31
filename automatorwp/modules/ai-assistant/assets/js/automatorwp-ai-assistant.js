var automatorwp_ai_assistant_history = [];
var automatorwp_ai_assistant_md_parser;

(function ( $ ) {

    // Init Showdown JS
    automatorwp_ai_assistant_md_parser = new showdown.Converter({
        smartIndentationFix: true,
        disableForced4SpacesIndentedSublists: true,
        simpleLineBreaks: true,
    });

    // Click on the assistant bubble
    $('body').on( 'click', '.automatorwp-ai-assistant-bubble', function(e){
        e.preventDefault();

        var bubble = $(this);
        var assistant = bubble.closest('.automatorwp-ai-assistant');
        var chat = assistant.find('.automatorwp-ai-assistant-chat');
        var help = assistant.find('.automatorwp-ai-assistant-help');

        // Toggle chat
        if( chat.data('status') === 'close' ) {
            chat.show();
            chat.data('status', 'open');
            bubble.removeClass('cmb-tooltip');

            // First open
            if( ! chat.find('.automatorwp-ai-assistant-message').length )
                automatorwp_ai_assistant_add_message( 'model', automatorwp_ai_assistant.i18n.first_message );
        } else {
            chat.hide();
            chat.data('status', 'close');
            bubble.addClass('cmb-tooltip');

            // Close help if open
            if( help.data('status') === 'open' ) {
                help.hide();
                help.data('status', 'close');
            }
        }
    });

    // Click on chat close
    $('body').on( 'click', '.automatorwp-ai-assistant-chat-close-button', function(e){
        e.preventDefault();

        var button = $(this);
        var assistant = button.closest('.automatorwp-ai-assistant');
        var bubble = assistant.find('.automatorwp-ai-assistant-bubble');

        bubble.trigger('click');
    });

    // Click on submit
    $('body').on( 'submit', '.automatorwp-ai-assistant-chat-form', function(e){
        e.preventDefault();
        automatorwp_ai_assistant_send_user_message();
    });

    // Click ENTER on input (Shift + ENTER grows the textarea)
    $('body').on( 'keypress', '.automatorwp-ai-assistant-chat-input', function(e){
        var keycode = ( e.keyCode ? e.keyCode : e.which );
        var $this = $(this);

        if(  keycode === 13 && e.shiftKey )
            $this.attr('rows', parseInt( $(this).attr('rows') ) + 1 );

        if( keycode !== 13  ) return;
        if( e.shiftKey ) return;
        e.preventDefault();

        automatorwp_ai_assistant_send_user_message();
    });

    // Change model select
    $('body').on( 'change', '.automatorwp-ai-assistant-chat-model-input', function(e){
        e.preventDefault();

        var text = $(this).find('option:selected').text();
        var $aux = $('<select/>').append( $('<option/>').text( text ) );

        $(this).after( $aux );
        $(this).width( $aux.width() + 10 );
        $aux.remove();
    });

    // Click on help
    $('body').on( 'click', '.automatorwp-ai-assistant-chat-help-button', function(e){
        e.preventDefault();

        var button = $(this);
        var assistant = button.closest('.automatorwp-ai-assistant');
        var help = assistant.find('.automatorwp-ai-assistant-help');

        if( help.data('status') === 'close' ) {
            help.show();
            help.data('status', 'open');
        } else {
            help.hide();
            help.data('status', 'close');
        }

    });

    // Click on send prompt
    $('body').on( 'click', '.automatorwp-ai-assistant-send-prompt', function(e){
        e.preventDefault();

        var $this = $(this);
        var prompt = $this.data('prompt');

        var assistant = $('.automatorwp-ai-assistant');
        var chat = assistant.find('.automatorwp-ai-assistant-chat');
        var input = assistant.find('.automatorwp-ai-assistant-chat-input');

        // Bail if input was disabled for something
        if( input.prop('disabled') ) return;

        if( chat.data('status') === 'close' ) {
            var bubble = assistant.find('.automatorwp-ai-assistant-bubble');
            bubble.trigger('click');
        }

        input.val( prompt );

        if( $this.data('send') )
            automatorwp_ai_assistant_send_user_message();

    });

    // Click on help ability (closed)
    $('body').on( 'click', '.automatorwp-ai-assistant-help-ability-close .automatorwp-ai-assistant-help-ability-label', function(e){
        e.preventDefault();

        var $this = $(this);
        var ability = $this.parent('.automatorwp-ai-assistant-help-ability');
        var opened = $('.automatorwp-ai-assistant-help-ability-open');

        if( opened ) {
            opened
                .removeClass('automatorwp-ai-assistant-help-ability-open')
                .addClass('automatorwp-ai-assistant-help-ability-close')
                .find('.automatorwp-ai-assistant-help-ability-desc').slideUp('fast');
        }

        ability
            .removeClass('automatorwp-ai-assistant-help-ability-close')
            .addClass('automatorwp-ai-assistant-help-ability-open')
            .find('.automatorwp-ai-assistant-help-ability-desc').slideDown('fast');


    });

    // Click on help ability (opened)
    $('body').on( 'click', '.automatorwp-ai-assistant-help-ability-open .automatorwp-ai-assistant-help-ability-label', function(e){
        e.preventDefault();

        var $this = $(this);
        var ability = $this.parent('.automatorwp-ai-assistant-help-ability');

        ability
            .removeClass('automatorwp-ai-assistant-help-ability-open')
            .addClass('automatorwp-ai-assistant-help-ability-close')
            .find('.automatorwp-ai-assistant-help-ability-desc').slideUp('fast');


    });

})( jQuery );

/**
 * Send user message to ajax
 *
 * @since 1.0.0
 */
function automatorwp_ai_assistant_send_user_message() {

    var $ = $ || jQuery;
    var loading = $('.automatorwp-ai-assistant-loading-message');

    // Bail if AI already loading
    if( loading.length ) return;

    var input = $('.automatorwp-ai-assistant-chat-input');
    var model = $('.automatorwp-ai-assistant-chat-model-input').val();

    if( input.prop('disabled') ) return;

    // Restore input height
    input.attr('rows', 1 );

    var prompt = input.val();

    // Prevent user HTML input
    prompt = prompt.replaceAll( '<', '&lt;' );
    prompt = prompt.replaceAll( '>', '&gt;' );

    if( prompt.length === 0 ) return;

    input.prop('disabled', true);

    automatorwp_ai_assistant_set_face('loading');
    automatorwp_ai_assistant_add_message( 'user', prompt );

    $.ajax({
        url: ajaxurl,
        method: 'POST',
        data: {
            action: 'automatorwp_ai_assistant_process_prompt',
            nonce: automatorwp_ai_assistant.nonce,
            prompt: prompt,
            model: model,
            history: automatorwp_ai_assistant_history,
        },
        success: function(r) {
            input.prop('disabled', false);

            automatorwp_ai_assistant_set_face('talking');

            var chat_message = $('.automatorwp-ai-assistant-message.automatorwp-ai-assistant-loading-message');
            chat_message.removeClass('automatorwp-ai-assistant-loading-message');

            // Add the original message to history
            automatorwp_ai_assistant_history.push( {
                author: 'model',
                text: r.data
            } );

            // Parse any possible markdown
            var html = automatorwp_ai_assistant_md_parser.makeHtml( r.data );

            // Remove "thinking" message
            chat_message.html( '' );

            if( ! r.success ) {
                chat_message.addClass('automatorwp-ai-assistant-message-error');
                automatorwp_ai_assistant_type( chat_message, html );
                return;
            }

            automatorwp_ai_assistant_type( chat_message, html );

        },
        error: function(r) {
            input.prop('disabled', false);

            automatorwp_ai_assistant_set_face('error');

            var chat_message = $('.automatorwp-ai-assistant-message.automatorwp-ai-assistant-loading-message');
            chat_message.removeClass('automatorwp-ai-assistant-loading-message');

            chat_message.addClass('automatorwp-ai-assistant-message-error');
            chat_message.html( automatorwp_ai_assistant.i18n.error_message );

            console.log(r);
        }
    });

    automatorwp_ai_assistant_history.push( {
        author: 'user',
        text: prompt
    } );

    input.val('');

    setTimeout( () => automatorwp_ai_assistant_add_message( 'model',  automatorwp_ai_assistant.i18n.loading ), 300)


}

/**
 * Add message to the chat history
 *
 * @since 1.0.0
 *
 * @param String author
 * @param String text
 */
function automatorwp_ai_assistant_add_message( author, text ) {

    var $ = $ || jQuery;

    if( ! text.length ) return;

    var history =  $('.automatorwp-ai-assistant-chat-history');
    var id = 'automatorwp-ai-assistant-message-' + history.find('.automatorwp-ai-assistant-message').length + 1;
    var css_class = 'automatorwp-ai-assistant-message';

    if( text === automatorwp_ai_assistant.i18n.loading ) {
        css_class += ' automatorwp-ai-assistant-loading-message'
    }

    var type_text = '';

    if( author === 'model' ) {
        type_text = text;
        text = '';
    }

    history
        .append( '<div id="' + id + '" class="' + css_class + '" data-author="' + author + '">' + text + '</div>' );

    if( type_text.length )
        automatorwp_ai_assistant_type( history.find('.automatorwp-ai-assistant-message#' + id), type_text );
}

// Typing effect vars
var automatorwp_ai_assistant_typing_text = '';
var automatorwp_ai_assistant_typing_speed = 20;
var automatorwp_ai_assistant_typing_in_html = false;

/**
 * Typing effect
 *
 * @since 1.0.0
 *
 * @param Object target
 * @param String text
 * @param Integer i
 * @param function callback
 */
function automatorwp_ai_assistant_type( target, text, i = 0, callback ) {

    var $ = $ || jQuery;

    if( i === 0 ) {
        if( text !== automatorwp_ai_assistant.i18n.loading )
            automatorwp_ai_assistant_set_face('talking');

        // Save the original text and strip the text to be written in chat
        automatorwp_ai_assistant_typing_text = text;

        var text_length = automatorwp_ai_assistant_strip_html( text ).length;

        // Define type speed based on text length
        if( text_length > 500 ) automatorwp_ai_assistant_typing_speed = 2;
        else if( text_length > 200 ) automatorwp_ai_assistant_typing_speed = 5;
        else if( text_length > 100 ) automatorwp_ai_assistant_typing_speed = 10;
        else automatorwp_ai_assistant_typing_speed = 20;
    }

    var char = text.slice( 0, i );

    target[0].innerHTML = char;

    var chat_history = target.closest('.automatorwp-ai-assistant-chat-history');
    chat_history[0].scrollTo(0, chat_history[0].scrollHeight);

    if ( i < text.length - 1 ) {
        var prev_char = char.slice(-1);

        // HTML start
        if( ! automatorwp_ai_assistant_typing_in_html && prev_char === '<' )
            automatorwp_ai_assistant_typing_in_html = true;

        // HTML end
        if( automatorwp_ai_assistant_typing_in_html && prev_char === '>' )
            automatorwp_ai_assistant_typing_in_html = false;

        if( automatorwp_ai_assistant_typing_in_html )
            return automatorwp_ai_assistant_type( target, text, i + 1 );

        setTimeout(() => automatorwp_ai_assistant_type( target, text, i + 1, callback ), automatorwp_ai_assistant_typing_speed );
    } else {
        target.html( automatorwp_ai_assistant_typing_text );

        automatorwp_ai_assistant_typing_in_html = false;

        if( text !== automatorwp_ai_assistant.i18n.loading )
            automatorwp_ai_assistant_set_face('idle');

        if (typeof callback == 'function') callback();
    }

}

/**
 * Strip HTML from text
 *
 * @since 1.0.0
 *
 * @param String text
 *
 * @returns {String}
 */
function automatorwp_ai_assistant_strip_html( text ) {
    // Bail if text does not have any HTML
    if( ! automatorwp_ai_assistant_has_html( text ) ) return text;

    var doc = new DOMParser().parseFromString(text, 'text/html');
    return doc.body.textContent || '';
}

/**
 * Check if text has HTML
 *
 * @since 1.0.0
 *
 * @param String text
 *
 * @returns {boolean}
 */
function automatorwp_ai_assistant_has_html( text ) {
    return( /<\/?[a-z][\s\S]*>/i.test( text ) );
}

/**
 * Set face status
 *
 * @since 1.0.0
 *
 * @param String status
 */
function automatorwp_ai_assistant_set_face( status ) {
    jQuery('.automatorwp-ai-assistant-face').attr('data-status', status);
}