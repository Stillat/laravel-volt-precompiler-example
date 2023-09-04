This repository contains the example code developed in the following blog post:

[Implementing a Custom Laravel Blade Precompiler for Volt and Livewire](https://stillat.com/blog/2023/09/04/implementing-a-custom-blade-precompiler-for-laravel-volt-and-livewire)

It provides an experimental Blade precompiler which compiles the following component syntax:

```blade
<!DOCTYPE html>
<html>
    <head>
        
        @livewireStyles
    </head>
    <body class="antialiased">

        <v-volt
                :count="10"
                @increment="fn() => $this->count++"
        >
            <button wire:click="increment">Increment</button>

            <p>Count: {{ $count }}</p>
        </v-volt>

        @livewireScripts
    </body>
</html>

```

into Livewire Volt class-based components behind the scenes.

## License

This example repository is free software, released under the MIT license.
