<script src="https://js.pusher.com/7.2.0/pusher.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@joeattardi/emoji-button@3.0.3/dist/index.min.js"></script>
<script >
    // Gloabl streamtalk variables from PHP to JS
    window.streamtalk = {
        name: "{{ config('streamtalk.name') }}",
        sounds: {!! json_encode(config('streamtalk.sounds')) !!},
        allowedImages: {!! json_encode(config('streamtalk.attachments.allowed_images')) !!},
        allowedFiles: {!! json_encode(config('streamtalk.attachments.allowed_files')) !!},
        maxUploadSize: {{ streamtalk::getMaxUploadSize() }},
        pusher: {!! json_encode(config('streamtalk.pusher')) !!},
        pusherAuthEndpoint: '{{route("pusher.auth")}}'
    };
    window.streamtalk.allAllowedExtensions = streamtalk.allowedImages.concat(streamtalk.allowedFiles);
</script>
<script src="{{ asset('js/streamtalk/utils.js') }}"></script>
<script src="{{ asset('js/streamtalk/code.js') }}"></script>
