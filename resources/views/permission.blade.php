@extends('default')

@section('content')
    @if (session('success'))
        <div class="container">
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        </div>
    @endif

    <div class="container">
        <div class="row">
            <form id="createPermissionForm" method="POST" action="{{ route('permission-create')}}">
                @csrf
                <div>
                    <label class="col-form-label mt-2" for="permission">New Permission:</label>
                    <input type="text" class="form-control" id="permission" name="permission" required>
                </div>

                <div>
                    <button type="submit" class="btn btn-success mt-2">Update Permissions</button>
                </div>
            </form>
        </div>
        <div class="row">
            <div class="col">
                <form id="updateRolePermissionsForm" method="POST" action="{{ route('permission-role') }}">
                    @csrf

                    <div>
                        <label class="col-form-label mt-2" for="role">Select Role:</label>
                        <select class="form-select" style="width:20rem" id="role" name="role" required>
                            @foreach ($roles as $role)
                                <option value="{{ $role->name }}">{{ $role->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="col-form-label">Select Permissions:</label>
                        @foreach ($permissions as $permission)
                            <div>
                                <label><input type="checkbox" name="permissions[]"
                                        value="{{ $permission->permission }}">{{ $permission->permission }}</label>
                            </div>
                        @endforeach

                        <div>
                            <button type="submit" class="btn btn-success mt-2">Update Permissions</button>
                        </div>
                    </div>
                </form>
            </div>
            <div class="col">
                <form id="updateUserPermissionsForm" method="POST" action="{{ route('permission-user') }}">
                    @csrf

                    <div>
                        <label class="col-form-label mt-2" for="user_id">Select User:</label>
                        <select class="form-select" style="width:20rem" id="user_id" name="user_id" required>
                            @foreach ($users as $user)
                                <option value="{{ $user->id }}">{{ $user->username }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="col-form-label">Select Permissions:</label>
                        @foreach ($permissions as $permission)
                            <div>
                                <label><input type="checkbox" name="permissions[]"
                                        value="{{ $permission->permission }}">{{ $permission->permission }}</label>
                            </div>
                        @endforeach
                        <div>
                            <button type="submit" class="btn btn-success mt-2">Update Permissions</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
