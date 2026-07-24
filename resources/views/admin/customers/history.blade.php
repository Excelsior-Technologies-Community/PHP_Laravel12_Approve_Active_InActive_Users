@extends('admin.layouts.app')

@section('content')

<div class="container-fluid">

    <div class="card shadow">

        <div class="card-header d-flex justify-content-between align-items-center">

            <div>

                <h4 class="mb-0">
                    Customer Audit History
                </h4>

                <small class="text-muted">

                    {{ $customer->name }}

                </small>

            </div>

            <a href="{{ route('admin.customers.index') }}"
                class="btn btn-secondary">

                Back

            </a>

        </div>

        <div class="card-body">

            @if($logs->count())

            <div class="table-responsive">

                <table class="table table-bordered table-hover">

                    <thead class="table-dark">

                        <tr>

                            <th>#</th>

                            <th>Action</th>

                            <th>Performed By</th>

                            <th>Description</th>

                            <th>Date</th>

                        </tr>

                    </thead>

                    <tbody>

                        @foreach($logs as $log)

                        <tr>

                            <td>{{ $loop->iteration }}</td>

                            <td>

                                @switch($log->action)

                                @case('Approved')
                                <span class="badge bg-success">
                                    Approved
                                </span>
                                @break

                                @case('Activated')
                                <span class="badge bg-primary">
                                    Activated
                                </span>
                                @break

                                @case('Deactivated')
                                <span class="badge bg-warning text-dark">
                                    Deactivated
                                </span>
                                @break

                                @case('Deleted')
                                <span class="badge bg-danger">
                                    Deleted
                                </span>
                                @break

                                @default
                                <span class="badge bg-info">
                                    Updated
                                </span>

                                @endswitch

                            </td>

                            <td>

                                {{ $log->admin->name }}

                            </td>

                            <td>

                                {{ $log->description }}

                            </td>

                            <td>

                                {{ $log->created_at->format('d M Y h:i A') }}

                            </td>

                        </tr>

                        @endforeach

                    </tbody>

                </table>

            </div>

            {{ $logs->links() }}

            @else

            <div class="alert alert-info">

                No history found.

            </div>

            @endif

        </div>

    </div>

</div>

@endsection