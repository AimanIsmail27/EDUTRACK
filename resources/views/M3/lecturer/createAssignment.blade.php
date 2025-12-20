@extends('layout.lecturer')

@section('title', 'Create Assessment')

@section('content')
<div class="max-w-5xl mx-auto space-y-8">
    <div>
        <p class="text-xs font-semibold tracking-widest text-indigo-500 uppercase">Assessment Module · M3</p>
        <h1 class="text-3xl font-black text-indigo-900">Create New Assessment</h1>
        <p class="text-indigo-900/60">Define the requirements, schedule, and grading weight for this assignment.</p>
    </div>

    <div class="strong-card rounded-3xl p-8">
        <form method="POST" action="{{ route('lecturer.assignments.store') }}" enctype="multipart/form-data">
            @include('M3.lecturer.partials.assignment-form', [
                'submitLabel' => 'Publish Assessment',
                'statuses' => $statuses,
            ])
        </form>
    </div>
</div>
@endsection
