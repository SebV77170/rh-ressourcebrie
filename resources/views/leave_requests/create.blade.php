@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card mb-4">
            <div class="card-header bg-white">
                <h1 class="h4 mb-0">Demander un congé</h1>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('leave-requests.store') }}">
                    @csrf
                    <div class="mb-3">
                        <label for="employee_name" class="form-label">Nom et prénom</label>
                        <input type="text" name="employee_name" id="employee_name" class="form-control" value="{{ old('employee_name') }}" required>
                    </div>
                    <div class="mb-3">
                        <label for="employee_email" class="form-label">Email</label>
                        <input type="email" name="employee_email" id="employee_email" class="form-control" value="{{ old('employee_email') }}" required>
                    </div>
                    <div class="row">
                        <div class="mb-3 col-md-6">
                            <label for="start_date" class="form-label">Date de début</label>
                            <input type="date" name="start_date" id="start_date" class="form-control" value="{{ old('start_date') }}" required>
                        </div>
                        <div class="mb-3 col-md-6">
                            <label for="end_date" class="form-label">Date de fin</label>
                            <input type="date" name="end_date" id="end_date" class="form-control" value="{{ old('end_date') }}" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="reason" class="form-label">Motif</label>
                        <textarea name="reason" id="reason" class="form-control" rows="3" placeholder="Optionnel">{{ old('reason') }}</textarea>
                    </div>
                    <div class="d-flex justify-content-end">
                        <a href="{{ route('leave-requests.index') }}" class="btn btn-outline-secondary me-2">Annuler</a>
                        <button class="btn btn-primary" type="submit">Envoyer la demande</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
