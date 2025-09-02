@extends('layouts.admin')

@php
// Define evaluation questions for each category - easily customizable in VS Code
$questions = [
    'A' => [
        'Demonstrates mastery of the subject matter.',
        'Explains concepts clearly and accurately.',
        'Provides relevant and up-to-date information.',
        'Answers questions effectively.',
        'Integrates theory and practice appropriately.'
    ],
    'B' => [
        'Uses varied teaching methods to engage students.',
        'Encourages student participation and interaction.',
        'Provides constructive feedback on assignments.',
        'Motivates students to achieve their potential.',
        'Creates a positive learning environment.'
    ],
    'C' => [
        'Maintains discipline and order in the classroom.',
        'Manages time effectively during class.',
        'Handles student behavior appropriately.',
        'Organizes classroom activities efficiently.',
        'Ensures a conducive learning atmosphere.'
    ],
    'D' => [
        'Arrives on time for classes.',
        'Completes the required number of class hours.',
        'Is present and prepared for all scheduled activities.'
    ],
    'E' => [
        'Speaks clearly and audibly.',
        'Uses appropriate language and tone.',
        'Communicates effectively with students.'
    ],
    'F' => [
        'Shows enthusiasm for teaching.',
        'Is approachable and supportive.',
        'Demonstrates professionalism.'
    ],
    'G' => [
        'Treats students with respect and fairness.',
        'Is available for consultation outside class.',
        'Builds positive relationships with students.'
    ],
    'H' => [
        'Grades assignments fairly and consistently.',
        'Provides clear grading criteria.'
    ]
];
@endphp

@section('content')
<div class="container-fluid">
    <h1 class="text-purple mb-4">Evaluate {{ $faculty->name }}</h1>

    <div class="card shadow">
        <div class="card-body">
            <form action="{{ route('admin.evaluation.store_for_faculty', $faculty->id) }}" method="POST">
                @csrf

                <div class="mb-3">
                    <div class="table table-bordered w-100" style="border:1px solid #ced4da">
                        <div class="row g-0 text-center fw-bold">
                            <div class="col-4 py-2 border-end">5 - Outstanding</div>
                            <div class="col-4 py-2 border-end">4 - Very Satisfactory</div>
                            <div class="col-4 py-2">3 - Satisfactory</div>
                        </div>
                        <div class="row g-0 text-center fw-bold border-top">
                            <div class="col-6 py-2 border-end">2 - Average</div>
                            <div class="col-6 py-2">1 - Needs Improvement</div>
                        </div>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-4">
                        <label class="form-label">Academic Year</label>
                        <select name="academic_year" class="form-select" required>
                            @foreach($academicYears as $year)
                                <option value="{{ $year }}">{{ $year }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Semester</label>
                        <select name="semester" class="form-select" required>
                            @foreach($semesters as $sem)
                                <option value="{{ $sem }}">{{ $sem }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Evaluation Period</label>
                        <select name="evaluation_period" class="form-select" required>
                            <option value="annual">Prelim</option>
                            <option value="midterm">Midterm</option>
                            <option value="final">Final</option>
                            
                        </select>
                    </div>
                </div>

                <div class="table-responsive mb-4">
                    <table class="table table-bordered align-middle">
                        <thead class="table-light">
                            <tr>
                                <th style="width:40%">Question</th>
                                <th class="text-center">1</th>
                                <th class="text-center">2</th>
                                <th class="text-center">3</th>
                                <th class="text-center">4</th>
                                <th class="text-center">5</th>
                            </tr>
                        </thead>

                        <tbody>
                            <tr class="table-secondary"><td colspan="6">A. Knowledge of the subject matter</td></tr>
                            @foreach($questions['A'] as $index => $question)
                            <tr>
                                <td>{{ $question }}</td>
                                @for($s=1;$s<=5;$s++)
                                    <td class="text-center"><input type="radio" name="group_A[q{{ $index + 1 }}]" value="{{ $s }}" required></td>
                                @endfor
                            </tr>
                            @endforeach

                            <tr class="table-secondary"><td colspan="6">B. Motivation strategy and techniques</td></tr>
                            @foreach($questions['B'] as $index => $question)
                            <tr>
                                <td>{{ $question }}</td>
                                @for($s=1;$s<=5;$s++)
                                    <td class="text-center"><input type="radio" name="group_B[q{{ $index + 1 }}]" value="{{ $s }}" required></td>
                                @endfor
                            </tr>
                            @endforeach

                            <tr class="table-secondary"><td colspan="6">C. Classroom management</td></tr>
                            @foreach($questions['C'] as $index => $question)
                            <tr>
                                <td>{{ $question }}</td>
                                @for($s=1;$s<=5;$s++)
                                    <td class="text-center"><input type="radio" name="group_C[q{{ $index + 1 }}]" value="{{ $s }}" required></td>
                                @endfor
                            </tr>
                            @endforeach

                            <tr class="table-secondary"><td colspan="6">D. Punctuality and attendance</td></tr>
                            @foreach($questions['D'] as $index => $question)
                            <tr>
                                <td>{{ $question }}</td>
                                @for($s=1;$s<=5;$s++)
                                    <td class="text-center"><input type="radio" name="group_D[q{{ $index + 1 }}]" value="{{ $s }}" required></td>
                                @endfor
                            </tr>
                            @endforeach

                            <tr class="table-secondary"><td colspan="6">E. Communication skills</td></tr>
                            @foreach($questions['E'] as $index => $question)
                            <tr>
                                <td>{{ $question }}</td>
                                @for($s=1;$s<=5;$s++)
                                    <td class="text-center"><input type="radio" name="group_E[q{{ $index + 1 }}]" value="{{ $s }}" required></td>
                                @endfor
                            </tr>
                            @endforeach

                            <tr class="table-secondary"><td colspan="6">F. Personality and attitude</td></tr>
                            @foreach($questions['F'] as $index => $question)
                            <tr>
                                <td>{{ $question }}</td>
                                @for($s=1;$s<=5;$s++)
                                    <td class="text-center"><input type="radio" name="group_F[q{{ $index + 1 }}]" value="{{ $s }}" required></td>
                                @endfor
                            </tr>
                            @endforeach

                            <tr class="table-secondary"><td colspan="6">G. Student faculty relation (Human Relations)</td></tr>
                            @foreach($questions['G'] as $index => $question)
                            <tr>
                                <td>{{ $question }}</td>
                                @for($s=1;$s<=5;$s++)
                                    <td class="text-center"><input type="radio" name="group_G[q{{ $index + 1 }}]" value="{{ $s }}" required></td>
                                @endfor
                            </tr>
                            @endforeach

                            <tr class="table-secondary"><td colspan="6">H. Fairness in grading</td></tr>
                            @foreach($questions['H'] as $index => $question)
                            <tr>
                                <td>{{ $question }}</td>
                                @for($s=1;$s<=5;$s++)
                                    <td class="text-center"><input type="radio" name="group_H[q{{ $index + 1 }}]" value="{{ $s }}" required></td>
                                @endfor
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mb-3">
                    <label class="form-label">What are the faculty's strong points?</label>
                    <textarea class="form-control" name="strengths" rows="3"></textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label">What are the faculty's areas for improvement?</label>
                    <textarea class="form-control" name="areas_for_improvement" rows="3"></textarea>
                </div>

                <div class="d-flex justify-content-end">
                    <a href="{{ route('admin.evaluation.faculty_summary') }}" class="btn btn-secondary me-2">Cancel</a>
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection


