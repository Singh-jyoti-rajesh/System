<form method="POST" action="{{ route('superadmin.login') }}">
    @csrf
    <input name="email" type="email" placeholder="Email" required>
    <input name="password" type="password" placeholder="Password" required>
    <button type="submit">Login as Super Admin</button>
</form>
