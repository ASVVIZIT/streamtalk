<script src="https://js.pusher.com/7.2.0/pusher.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@joeattardi/emoji-button@3.0.3/dist/index.min.js"></script>
<script >
    // Gloabl StreamTalk variables from PHP to JS
    window._Stream_Talk = {
        name: "{{ config('streamtalk.name') }}",
        sounds: {!! json_encode(config('streamtalk.sounds')) !!},
        allowedImages: {!! json_encode(config('streamtalk.attachments.allowed_images')) !!},
        allowedFiles: {!! json_encode(config('streamtalk.attachments.allowed_files')) !!},
        maxUploadSize: {{ StreamTalk::getMaxUploadSize() }},
        pusher: {!! json_encode(config('streamtalk.pusher')) !!},
        pusherAuthEndpoint: '{{route("pusher.auth")}}'
    };
    window._Stream_Talk.allAllowedExtensions = _Stream_Talk.allowedImages.concat(_Stream_Talk.allowedFiles);
</script>
<script src="{{ asset('js/StreamTalk/utils.js') }}"></script>
<script src="{{ asset('js/StreamTalk/code.js') }}"></script>
