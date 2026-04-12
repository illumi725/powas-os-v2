<x-mail::message>
# Reset User Password

Your password is successfully reset! Login to your account to change your temporary password.

> Username: **{{ $username }}** <br>
> Temporary Password: **{{ $newpassword }}**

Thanks, <br>
{{ config('app.name') }}
</x-mail::message>
