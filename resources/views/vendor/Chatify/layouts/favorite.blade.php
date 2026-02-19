<div class="favorite-list-item" style="padding-left:5px " >
    @if($user)
        <div data-id="{{ $user->id }}" data-action="0" class="avatar av-m"
            style="background-image: url('{{ Chatify::getUserWithAvatar($user)->avatar }}');margin: auto">

        </div>
                <p style="">{{ strlen($user->name) > 40 ? substr($user->name,0,40).'..' : $user->name }}</p>
    @endif
</div>
