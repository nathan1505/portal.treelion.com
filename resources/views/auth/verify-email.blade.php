<x-guest-layout>
    <x-jet-authentication-card>
        <x-slot name="logo">
            <img src={{ URL::asset('images/elion-logo.png') }}>
        </x-slot>

        <div class="mb-4 text-sm text-gray-600">
            {{ __('感谢注册！在开始之前，请从您的注册邮箱中点击链接来验证您的邮箱地址。如果您未收到邮件，清点击重新发送。') }}
        </div>

        @if (session('status') == 'verification-link-sent')
            <div class="mb-4 font-medium text-sm text-green-600">
                {{ __('一个新的验证链接已经发送至您注册时使用的邮箱地址') }}
            </div>
        @endif

        <div class="mt-4 flex items-center justify-between">
            <form method="POST" action="{{ route('verification.send') }}">
                @csrf

                <div>
                    <x-jet-button type="submit">
                        {{ __('重发验证链接') }}
                    </x-jet-button>
                </div>
            </form>

            <form method="POST" action="{{ route('logout') }}">
                @csrf

                <button type="submit" class="underline text-sm text-gray-600 hover:text-gray-900">
                    {{ __('登出') }}
                </button>
            </form>
        </div>
    </x-jet-authentication-card>
</x-guest-layout>
