<?php

use App\Livewire\Forms\LoginForm;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public LoginForm $form;

    public function login(): void
    {
        $this->validate();

        $this->form->authenticate();

        Session::regenerate();

        $this->redirectIntended(default: route('dashboard', absolute: false), navigate: true);
    }
}; ?>

<div x-data="{ showPassword: false }">
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form wire:submit="login" class="space-y-5">
        <!-- Correo Electrónico -->
        <div>
            <label for="email" class="block text-[11px] font-bold uppercase tracking-[0.18em] text-blue-100/90 mb-2">
                Correo Institucional
            </label>
            <div class="relative">
                <i class="fas fa-envelope absolute left-4 top-3.5 text-blue-200/50 text-sm pointer-events-none"></i>
                <input
                    wire:model="form.email"
                    id="email"
                    type="email"
                    name="email"
                    required
                    autofocus
                    autocomplete="username"
                    placeholder="admin@marinacgyc.com"
                    class="w-full pl-11 pr-4 py-3.5 glass-card rounded-2xl text-sm text-white placeholder-blue-200/40 focus:bg-white/15 focus:ring-2 focus:ring-blue-400/40 focus:border-white/30 outline-none transition-all"
                >
            </div>
            @error('form.email')
            <p class="mt-2 text-xs font-semibold text-rose-400 flex items-center animate-fade-in">
                <i class="fas fa-circle-exclamation mr-1.5"></i> {{ $message }}
            </p>
            @enderror
        </div>

        <!-- Contraseña -->
        <div>
            <label for="password" class="block text-[11px] font-bold uppercase tracking-[0.18em] text-blue-100/90 mb-2">
                Contraseña de Acceso
            </label>
            <div class="relative">
                <i class="fas fa-lock absolute left-4 top-3.5 text-blue-200/50 text-sm pointer-events-none"></i>
                <input
                    wire:model="form.password"
                    id="password"
                    :type="showPassword ? 'text' : 'password'"
                    name="password"
                    required
                    autocomplete="current-password"
                    placeholder="••••••••••••"
                    class="w-full pl-11 pr-11 py-3.5 glass-card rounded-2xl text-sm text-white placeholder-blue-200/40 focus:bg-white/15 focus:ring-2 focus:ring-blue-400/40 focus:border-white/30 outline-none transition-all"
                >
                <button
                    type="button"
                    @click="showPassword = !showPassword"
                    class="absolute right-3.5 top-3 text-blue-200/60 hover:text-white transition-colors focus:outline-none p-1"
                    title="Mostrar / Ocultar contraseña"
                >
                    <i class="fas text-sm" :class="showPassword ? 'fa-eye-slash text-rose-400' : 'fa-eye'"></i>
                </button>
            </div>
            @error('form.password')
            <p class="mt-2 text-xs font-semibold text-rose-400 flex items-center animate-fade-in">
                <i class="fas fa-circle-exclamation mr-1.5"></i> {{ $message }}
            </p>
            @enderror
        </div>

        <!-- Opciones adicionales -->
        <div class="flex items-center justify-between pt-1">
            <label for="remember" class="inline-flex items-center cursor-pointer select-none group">
                <input
                    wire:model="form.remember"
                    id="remember"
                    type="checkbox"
                    name="remember"
                    class="rounded-md bg-white/10 border-white/25 text-[#C8102E] shadow-sm focus:ring-[#C8102E]/40 cursor-pointer"
                >
                <span class="ms-2 text-xs font-medium text-blue-100/80 group-hover:text-white transition-colors">
                    Mantener sesión activa
                </span>
            </label>

            @if (Route::has('password.request'))
                <a
                    href="{{ route('password.request') }}"
                    wire:navigate
                    class="text-xs font-semibold text-blue-200/70 hover:text-white transition-colors"
                >
                    ¿Olvidó su clave?
                </a>
            @endif
        </div>

        <!-- Botón de Inicio de Sesión con Efecto Apple Lift -->
        <div class="pt-3">
            <button
                type="submit"
                wire:loading.attr="disabled"
                class="btn-apple-crimson w-full py-4 px-6 rounded-2xl font-bold text-xs uppercase tracking-[0.2em] flex items-center justify-center gap-2.5 disabled:opacity-60 disabled:cursor-not-allowed"
            >
                <span wire:loading.remove wire:target="login" class="flex items-center gap-2">
                    <span>Ingresar a la Marina</span>
                    <i class="fas fa-arrow-right text-[11px]"></i>
                </span>

                <span wire:loading wire:target="login" class="flex items-center gap-2">
                    <i class="fas fa-circle-notch fa-spin"></i>
                    <span>Autenticando...</span>
                </span>
            </button>
        </div>
    </form>
</div>
