<div id="header-main">
    <h1><img src="img/copytube_logo.png" alt="CopyTube Logo"></h1>
</div>
<div id="header-sub">
    @if ($username !== '')
    <a class="menu-item" href="/home">Home</a>
    @endif
    <a class="menu-item" href="/register">Register</a>
    <a class="menu-item" href="/login">Login</a>
    <a class="menu-item" href="/chat">Chat</a>
    @if ($username !== '')
    <i class="gear"></i>
    <div class="hide gear-dropdown">
        <p>Hello {{ $username }}</p>
        <ul>
            <li><a href="/logout">Log out</a></li>
        </ul>
    </div>
    @endif
</div>
