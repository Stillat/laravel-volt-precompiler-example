<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Laravel</title>

        @livewireStyles
    </head>
    <body class="antialiased">

        <v-volt
                :count="10"
                @increment="fn() => $this->count++"
        >
            <button wire:click="increment">Increment</button>

            <p>Count: {{ $count }}</p>

            <hr />
            <v-volt
                    :count="20"
                    @increment="fn() => $this->count++"
            >
                <button wire:click="increment">Increment</button>

                <p>Count: {{ $count }}</p>

                <hr />
            </v-volt>
        </v-volt>

        @livewireScripts
    </body>
</html>
