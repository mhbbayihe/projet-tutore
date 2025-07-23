<div>
    <div class="container mx-auto px-4 py-8">
        <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg p-6">
            <h2 class="text-xl font-semibold mb-4 text-gray-900 dark:text-gray-100">Liste des Utilisateurs</h2>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Nom
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Email
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Téléphone
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Rôle
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Statut
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse ($users as $user)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10">
                                            @if (!empty($user->profile))
                                                <img class="h-10 w-10 rounded-full object-cover" src="{{ Storage::url($user->profile) }}" alt="{{ $user->name }} Avatar">
                                            @else
                                                <div class="h-10 w-10 rounded-full bg-gray-200 dark:bg-gray-700 flex items-center justify-center text-gray-800 dark:text-gray-200 font-bold uppercase">
                                                    @php
                                                        $nameParts = explode(' ', $user->name);
                                                        $initials = '';
                                                        if (count($nameParts) > 0) {
                                                            $initials .= strtoupper(substr($nameParts[0], 0, 1));
                                                        }
                                                        if (count($nameParts) > 1) {
                                                            $initials .= strtoupper(substr(end($nameParts), 0, 1));
                                                        } elseif (count($nameParts) == 1 && strlen($nameParts[0]) > 1) {
                                                            $initials = strtoupper(substr($nameParts[0], 0, 2));
                                                        }
                                                        if (empty($initials) && !empty($user->name)) {
                                                            $initials = strtoupper(substr($user->name, 0, 1));
                                                        } elseif (empty($initials)) {
                                                            $initials = '?';
                                                        }
                                                    @endphp
                                                    {{ $initials }}
                                                </div>
                                            @endif
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                                {{ $user->name }} {{ $user->surname }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900 dark:text-gray-100">{{ $user->email }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                    {{ $user->phone ?? 'N/A' }}
                                TD>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                    {{ ucfirst($user->role) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if ($user->status === 1)
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-700 dark:text-green-100">
                                            Actif
                                        </span>
                                    @else
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800 dark:bg-red-700 dark:text-red-100">
                                            Bloqué
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-500 dark:text-gray-400">
                                    Aucun utilisateur trouvé.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $users->links() }}
            </div>
        </div>
    </div>
</div>
