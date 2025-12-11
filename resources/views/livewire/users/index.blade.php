<div class="space-y-6">
    <!-- Header -->
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-bame-dark mb-2">User Management</h2>
        <p class="text-sm text-gray-500">Manage all users in the system. Create, edit, and delete user profiles.</p>
    </div>

    <!-- Flash Messages -->
    @if (session()->has('success'))
        <div class="mb-4 bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-xl flex items-center gap-3">
            <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
            {{ session('success') }}
        </div>
    @endif

    @if (session()->has('error'))
        <div class="mb-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl flex items-center gap-3">
            <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            {{ session('error') }}
        </div>
    @endif

    <!-- Search and Filters -->
    <div class="bg-white rounded-2xl border border-gray-200 p-4 mb-6 shadow-sm">
        <div class="flex items-center justify-between gap-4">
            <!-- Search -->
            <div class="flex-1 relative">
                <svg class="w-5 h-5 text-gray-400 absolute left-3 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input 
                    type="search" 
                    wire:model.live="search"
                    placeholder="Search by name or email"
                    class="w-full pl-10 pr-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-bame-dark placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-bame-purple/50 focus:border-bame-purple"
                >
            </div>

            <!-- Role Filter -->
            <div class="flex items-center bg-gray-100 rounded-xl p-1">
                <button 
                    wire:click="$set('roleFilter', '')"
                    class="px-4 py-2 rounded-lg text-sm font-medium transition {{ $roleFilter === '' ? 'bg-white text-bame-purple shadow-sm' : 'text-gray-600 hover:text-gray-900' }}"
                >
                    All Roles
                </button>
                <button 
                    wire:click="$set('roleFilter', 'admin')"
                    class="px-4 py-2 rounded-lg text-sm font-medium transition {{ $roleFilter === 'admin' ? 'bg-white text-bame-purple shadow-sm' : 'text-gray-600 hover:text-gray-900' }}"
                >
                    Admin
                </button>
                <button 
                    wire:click="$set('roleFilter', 'consultant')"
                    class="px-4 py-2 rounded-lg text-sm font-medium transition {{ $roleFilter === 'consultant' ? 'bg-white text-bame-purple shadow-sm' : 'text-gray-600 hover:text-gray-900' }}"
                >
                    Consultant
                </button>
                <button 
                    wire:click="$set('roleFilter', 'executive')"
                    class="px-4 py-2 rounded-lg text-sm font-medium transition {{ $roleFilter === 'executive' ? 'bg-white text-bame-purple shadow-sm' : 'text-gray-600 hover:text-gray-900' }}"
                >
                    Executive
                </button>
                <button 
                    wire:click="$set('roleFilter', 'approver')"
                    class="px-4 py-2 rounded-lg text-sm font-medium transition {{ $roleFilter === 'approver' ? 'bg-white text-bame-purple shadow-sm' : 'text-gray-600 hover:text-gray-900' }}"
                >
                    Approver
                </button>
            </div>

            <!-- Create Button -->
            <button 
                wire:click="openCreateModal"
                class="inline-flex items-center gap-2 px-5 py-2.5 text-white text-sm font-semibold rounded-lg transition-all duration-200 shadow-lg hover:shadow-xl hover:scale-105"
                style="background: linear-gradient(135deg, #FF00FF 0%, #6B46C1 100%);"
            >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Create User
            </button>
        </div>
    </div>

    <!-- Users Table -->
    <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden shadow-sm">
        <table class="w-full">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Name</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Email</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Role</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-4 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($users as $user)
                    <tr class="hover:bg-gray-50 transition-colors duration-150">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl flex items-center justify-center text-white font-bold text-sm" style="background: linear-gradient(135deg, #FF00FF 0%, #6B46C1 100%);">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </div>
                                <div class="text-sm font-semibold text-bame-dark">{{ $user->name }}</div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-600">{{ $user->email }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-3 py-1.5 inline-flex text-xs leading-5 font-semibold rounded-lg
                                {{ $user->role === 'admin' ? 'bg-purple-100 text-purple-700' : '' }}
                                {{ $user->role === 'consultant' ? 'bg-blue-100 text-blue-700' : '' }}
                                {{ $user->role === 'executive' ? 'bg-emerald-100 text-emerald-700' : '' }}
                                {{ $user->role === 'approver' ? 'bg-amber-100 text-amber-700' : '' }}">
                                {{ ucfirst($user->role) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs leading-5 font-semibold rounded-lg bg-emerald-100 text-emerald-700">
                                <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full"></span>
                                Active
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <button 
                                wire:click="editUser({{ $user->id }})"
                                class="p-2 text-gray-400 hover:text-bame-purple hover:bg-purple-50 rounded-lg transition"
                                title="Edit user"
                            >
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                            </button>
                            <button 
                                wire:click="deleteUser({{ $user->id }})"
                                wire:confirm="Are you sure you want to delete this user?"
                                class="p-2 text-gray-400 hover:text-red-500 hover:bg-red-50 rounded-lg transition"
                                title="Delete user"
                            >
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-16 text-center">
                            <div class="w-20 h-20 rounded-2xl flex items-center justify-center mx-auto mb-4" style="background: rgba(107, 70, 193, 0.1);">
                                <svg class="w-10 h-10 text-bame-purple" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                                </svg>
                            </div>
                            <p class="text-gray-600 font-medium">No users found</p>
                            <p class="text-sm text-gray-400 mt-1">Try adjusting your search or filters</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Create/Edit Modal -->
    @if($showModal)
        <div 
            x-data="{ show: true }"
            x-show="show"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            class="fixed inset-0 z-50 overflow-y-auto"
        >
            <!-- Backdrop -->
            <div class="fixed inset-0 bg-black/50 backdrop-blur-sm" wire:click="closeModal"></div>

            <!-- Modal -->
            <div class="flex items-center justify-center min-h-screen p-4">
                <div class="relative bg-white rounded-2xl shadow-2xl max-w-md w-full" @click.stop>
                    <!-- Header -->
                    <div class="flex items-center justify-between p-6 border-b border-gray-200">
                        <h2 class="text-xl font-bold text-bame-dark">
                            {{ $editingUserId ? 'Edit User' : 'Create New User' }}
                        </h2>
                        <button wire:click="closeModal" class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>

                    <!-- Form -->
                    <form wire:submit="saveUser" class="p-6">
                        <div class="space-y-4">
                            <!-- Name -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Name</label>
                                <input 
                                    type="text" 
                                    wire:model="name"
                                    class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-bame-dark placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-bame-purple/50 focus:border-bame-purple"
                                    placeholder="John Doe"
                                >
                                @error('name') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                            </div>

                            <!-- Email -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                                <input 
                                    type="email" 
                                    wire:model="email"
                                    class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-bame-dark placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-bame-purple/50 focus:border-bame-purple"
                                    placeholder="john@example.com"
                                >
                                @error('email') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                            </div>

                            <!-- Password -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Password {{ $editingUserId ? '(leave blank to keep current)' : '' }}
                                </label>
                                <input 
                                    type="password" 
                                    wire:model="password"
                                    class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-bame-dark placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-bame-purple/50 focus:border-bame-purple"
                                    placeholder="••••••••"
                                >
                                @error('password') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                            </div>

                            <!-- Role -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Role</label>
                                <select 
                                    wire:model="role"
                                    class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-bame-dark focus:outline-none focus:ring-2 focus:ring-bame-purple/50 focus:border-bame-purple"
                                >
                                    <option value="admin">Admin</option>
                                    <option value="consultant">Consultant</option>
                                    <option value="executive">Executive</option>
                                    <option value="approver">Approver</option>
                                </select>
                                @error('role') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <!-- Footer -->
                        <div class="flex items-center justify-end space-x-3 mt-6 pt-6 border-t border-gray-200">
                            <button 
                                type="button"
                                wire:click="closeModal"
                                class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium rounded-xl transition"
                            >
                                Cancel
                            </button>
                            <button 
                                type="submit"
                                class="px-5 py-2.5 text-white font-semibold rounded-xl transition-all duration-200 hover:scale-105"
                                style="background: linear-gradient(135deg, #FF00FF 0%, #6B46C1 100%);"
                                wire:loading.attr="disabled"
                            >
                                <span wire:loading.remove>{{ $editingUserId ? 'Update User' : 'Create User' }}</span>
                                <span wire:loading>Saving...</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>
