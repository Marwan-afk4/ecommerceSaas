<div>
    <x-dashboard.page-header title="Store admins" description="Owners can invite other admins to help run this store." />

    @if ($canManageAdmins)
        <form wire:submit="addAdmin" class="mb-6 grid gap-3 rounded-2xl border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-800 dark:bg-zinc-900 lg:grid-cols-2">
            <x-dashboard.input wire:model="name" type="text" placeholder="Full name" />
            <x-dashboard.input wire:model="email" type="email" placeholder="Email" />
            <x-dashboard.input wire:model="password" type="password" placeholder="Password (new accounts only)" />
            <x-dashboard.select wire:model="role">
                @foreach ($roles as $shopRole)
                    <option value="{{ $shopRole->value }}">{{ ucfirst($shopRole->value) }}</option>
                @endforeach
            </x-dashboard.select>
            <div class="lg:col-span-2">
                <x-dashboard.button type="submit">Add admin</x-dashboard.button>
            </div>
            @error('name') <p class="text-sm text-red-600">{{ $message }}</p> @enderror
            @error('email') <p class="text-sm text-red-600">{{ $message }}</p> @enderror
            @error('password') <p class="text-sm text-red-600">{{ $message }}</p> @enderror
            @error('role') <p class="text-sm text-red-600">{{ $message }}</p> @enderror
        </form>
    @else
        <p class="mb-6 rounded-xl bg-zinc-100 px-4 py-3 text-sm text-zinc-600 dark:bg-zinc-800 dark:text-zinc-300">Only the store owner can add admins.</p>
    @endif

    <div class="overflow-hidden rounded-2xl border border-zinc-200 bg-white shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
        <table class="w-full text-left text-sm">
            <thead class="border-b border-zinc-200 bg-zinc-50 dark:border-zinc-800 dark:bg-zinc-950">
                <tr>
                    <th class="px-4 py-3 font-medium">Name</th>
                    <th class="px-4 py-3 font-medium">Email</th>
                    <th class="px-4 py-3 font-medium">Role</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($admins as $admin)
                    <tr class="border-b border-zinc-100 dark:border-zinc-800" wire:key="admin-{{ $admin->id }}">
                        <td class="px-4 py-3">{{ $admin->name }}</td>
                        <td class="px-4 py-3">{{ $admin->email }}</td>
                        <td class="px-4 py-3 capitalize">{{ $admin->pivot->role->value }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
