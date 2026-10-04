<x-account-layout active="profile" :title="__('Profile')">

    <div class="space-y-14 max-w-xl">
        <section>
            @include('profile.partials.update-profile-information-form')
        </section>

        <div class="hairline-gold"></div>

        <section>
            @include('profile.partials.update-password-form')
        </section>

        <div class="hairline-gold"></div>

        <section>
            @include('profile.partials.delete-user-form')
        </section>
    </div>

</x-account-layout>
