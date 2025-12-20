@extends('layout.lecturer')

@section('title', 'Edit Assessment')

@section('content')
<div class="max-w-5xl mx-auto space-y-8">
    <div>
        <p class="text-xs font-semibold tracking-widest text-indigo-500 uppercase">Assessment Module · M3</p>
        <h1 class="text-3xl font-black text-indigo-900">Edit Assessment</h1>
        <p class="text-indigo-900/60">Update instructions, due dates, or grading weights as needed.</p>
    </div>

    <div class="strong-card rounded-3xl p-8">
        <form method="POST" action="{{ route('lecturer.assignments.update', $assignment) }}" enctype="multipart/form-data">
            @method('PUT')
            @include('M3.lecturer.partials.assignment-form', [
                'submitLabel' => 'Save Changes',
                'statuses' => $statuses,
            ])
        </form>
    </div>
</div>
@endsection
