@extends('layouts.app')

@section('header')
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        My Requests
    </h2>
@endsection

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <!-- Header -->
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-lg font-semibold">My Requests</h3>
                    <a href="{{ route('customer.requests.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">
                        <i class="bi bi-plus-circle me-1"></i> New Request
                    </a>
                </div>

                <!-- Requests List -->
                @if($requests->isEmpty())
                    <div class="text-center py-8">
                        <i class="bi bi-inbox text-gray-300 text-4xl"></i>
                        <p class="text-gray-500 mt-2">No requests yet</p>
                        <a href="{{ route('customer.requests.create') }}" class="inline-block mt-4 bg-blue-600 text-white px-4 py-2 rounded-md">
                            Create Your First Request
                        </a>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">ID</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Title</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Submitted</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($requests as $request)
                                <tr>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">#{{ $request->id }}</td>
                                    <td class="px-4 py-3">
                                        <div class="font-medium text-gray-900">{{ $request->title }}</div>
                                        <div class="text-sm text-gray-500">{{ Str::limit($request->description, 50) }}</div>
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">
                                            {{ $request->getTypeLabel() }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        @php
                                            $statusColors = [
                                                'pending' => 'bg-yellow-100 text-yellow-800',
                                                'approved' => 'bg-green-100 text-green-800',
                                                'rejected' => 'bg-red-100 text-red-800',
                                                'in_progress' => 'bg-blue-100 text-blue-800'
                                            ];
                                        @endphp
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $statusColors[$request->status] ?? 'bg-gray-100' }}">
                                            {{ ucfirst($request->status) }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">
                                        {{ $request->created_at->format('M d, Y') }}
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm font-medium">
                                        <a href="{{ route('customer.requests.show', $request) }}" class="text-blue-600 hover:text-blue-900 mr-3">
                                            View
                                        </a>
                                        @if($request->status == 'pending')
                                            <form action="{{ route('customer.requests.destroy', $request) }}" method="POST" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900" 
                                                        onclick="return confirm('Cancel this request?')">
                                                    Cancel
                                                </button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="mt-4">
                        {{ $requests->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection