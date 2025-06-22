{{-- user info and avatar --}}
@if(isset($user) && $user)
    <div class="avatar av-l StreamTalk-d-flex"
         style="background-image: url('{{ StreamTalk::getUserWithAvatar($user)->avatar }}');">
    </div>
    <p class="info-name">{{ $user->name }}</p>
@else
    <div class="avatar av-l StreamTalk-d-flex"></div>
    <p class="info-name">{{ config('streamtalk.name') }}</p>
@endif

<div class="messenger-infoView-btns">
    <a href="#" class="danger delete-conversation">Удалить беседу</a>
</div>
{{-- shared photos --}}
<div class="messenger-infoView-shared">
    <p class="messenger-title"><span>Общие фотографии</span></p>
    <div class="shared-photos-list"></div>
</div>
