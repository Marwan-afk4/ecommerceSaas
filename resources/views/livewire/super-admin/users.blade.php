<div>
    <x-dashboard.page-header title="Users" description="Platform admins and store members." />

    <div class="overflow-hidden rounded-2xl border border-zinc-200 bg-white shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
        <table class="w-full text-left text-sm">
            <thead class="border-b border-zinc-200 bg-zinc-50 dark:border-zinc-800 dark:bg-zinc-950">
                <tr>
                    <th class="px-4 py-3 font-medium">Name</th>
                    <th class="px-4 py-3 font-medium">Email</th>
                    <th class="px-4 py-3 font-medium">Role</th>
                    <th class="px-4 py-3 font-medium">Stores</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($users as $user)
                    <tr class="border-b border-zinc-100 dark:border-zinc-800" wire:key="user-{{ $user->id }}">
                        <td class="px-4 py-3">{{ $user->name }}</td>
                        <td class="px-4 py-3">{{ $user->email }}</td>
                        <td class="px-4 py-3">{{ $user->is_platform_admin ? 'Superadmin' : 'Store admin' }}</td>
                        <td class="px-4 py-3">{{ $user->shops->pluck('name')->join(', ') ?: '—' }}</td>
                        <td class="px-4 py-3 text-right">
                            @if (auth()->id() !== $user->id)
                                <button type="button" wire:click="deleteUser({{ $user->id }})" wire:confirm="Delete this user?" class="text-red-600 hover:underline">
                                    Delete
                                </button>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $users->links() }}</div>
</div>
