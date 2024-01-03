<x-filament-widgets::widget class="fi-filament-info-widget">
    <x-filament::section>
        <div class="flex items-center gap-x-3">
            <div class="flex-1">
                <x-filament::link :href="url('home')">
                    Home Page
                </x-filament::link>
                <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                    Go to home page.
                </p>
            </div>
            <div class="flex-1">
                <x-filament::link :href="url('products')">
                    Products Page
                </x-filament::link>
                <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                    Go to products page.
                </p>
            </div>
            <div class="flex-1">
                <x-filament::link :href="url('about-us')">
                    About Us Page
                </x-filament::link>
                <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                    Go to about page.
                </p>
            </div>
            <div class="flex-1">
                <x-filament::link :href="url('contact-us')">
                    Contact Us Page
                </x-filament::link>
                <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                    Go to contact us page.
                </p>
            </div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>