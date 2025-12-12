@extends('layout.administrator')

@section('title', 'Admin Dashboard')

@section('content')
<div class="pb-6">
<p class="mt-1 text-sm text-gray-500">Welcome to the administration panel. Here are your key metrics.</p>
</div>

<div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
    <div class="bg-white overflow-hidden shadow-lg rounded-xl p-5 transform hover:scale-[1.02] transition duration-300">
        <div class="flex items-center">
            <div class="flex-shrink-0 bg-indigo-100 rounded-lg p-3">
                <svg class="h-6 w-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857m0 0a5.002 5.002 0 019.288 0M7 15h3.5l-2.5-3 2.5-3H7"></path></svg>
            </div>
            <div class="ml-4 w-0 flex-1">
                <dl>
                    <dt class="text-sm font-medium text-gray-500 truncate">Total Students</dt>
                    <dd class="text-3xl font-extrabold text-gray-900">4,520</dd>
                </dl>
            </div>
        </div>
    </div>

    <div class="bg-white overflow-hidden shadow-lg rounded-xl p-5 transform hover:scale-[1.02] transition duration-300">
        <div class="flex items-center">
            <div class="flex-shrink-0 bg-green-100 rounded-lg p-3">
                <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M12 20.354c-2.455 0-4.646-.889-6.075-2.318A9.957 9.957 0 0112 11.002c2.455 0 4.646.889 6.075 2.318A9.957 9.957 0 0112 20.354z"></path></svg>
            </div>
            <div class="ml-4 w-0 flex-1">
                <dl>
                    <dt class="text-sm font-medium text-gray-500 truncate">Total Lecturers</dt>
                    <dd class="text-3xl font-extrabold text-gray-900">185</dd>
                </dl>
            </div>
        </div>
    </div>

    <div class="bg-white overflow-hidden shadow-lg rounded-xl p-5 transform hover:scale-[1.02] transition duration-300">
        <div class="flex items-center">
            <div class="flex-shrink-0 bg-yellow-100 rounded-lg p-3">
                <svg class="h-6 w-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
            </div>
            <div class="ml-4 w-0 flex-1">
                <dl>
                    <dt class="text-sm font-medium text-gray-500 truncate">Active Courses</dt>
                    <dd class="text-3xl font-extrabold text-gray-900">45</dd>
                </dl>
            </div>
        </div>
    </div>
    
    <div class="bg-white overflow-hidden shadow-lg rounded-xl p-5 transform hover:scale-[1.02] transition duration-300">
        <div class="flex items-center">
            <div class="flex-shrink-0 bg-red-100 rounded-lg p-3">
                <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.332 16c-.77 1.333.192 3 1.732 3z"></path></svg>
            </div>
            <div class="ml-4 w-0 flex-1">
                <dl>
                    <dt class="text-sm font-medium text-gray-500 truncate">Pending Actions</dt>
                    <dd class="text-3xl font-extrabold text-gray-900">7</dd>
                </dl>
            </div>
        </div>
    </div>
</div>

<div class="mt-8 grid grid-cols-1 lg:grid-cols-3 gap-6">
    
    <div class="bg-white shadow-lg rounded-xl p-6 lg:col-span-2">
        <h2 class="text-xl font-semibold text-gray-800 border-b pb-3 mb-4">Recent System Activity</h2>
        <ul class="divide-y divide-gray-200">
            <li class="py-3 flex justify-between items-center text-sm">
                <span class="text-gray-600">Student registration pending approval (ID S5001).</span>
                <span class="text-xs text-indigo-600 font-medium">Pending</span>
            </li>
            <li class="py-3 flex justify-between items-center text-sm">
                <span class="text-gray-600">New Lecturer Profile (Dr. Alex Lee) registered.</span>
                <span class="text-xs text-gray-400">2 hours ago</span>
            </li>
            <li class="py-3 flex justify-between items-center text-sm">
                <span class="text-gray-600">Course BCN1015 (Data Mining) updated by Admin.</span>
                <span class="text-xs text-gray-400">1 day ago</span>
            </li>
            <li class="py-3 flex justify-between items-center text-sm">
                <span class="text-gray-600">System backup successful.</span>
                <span class="text-xs text-gray-400">2 days ago</span>
            </li>
        </ul>
    </div>
    
    <div class="bg-white shadow-lg rounded-xl p-6">
        <h2 class="text-xl font-semibold text-gray-800 border-b pb-3 mb-4">Quick Actions</h2>
        <div class="space-y-3">
            <a href="#" class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 transition-colors">
                Register New Student
            </a>
            <a href="#" class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg shadow-sm text-indigo-700 bg-indigo-100 hover:bg-indigo-200 transition-colors">
                Add New Course
            </a>
            <a href="#" class="w-full inline-flex justify-center items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-lg shadow-sm text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                View System Logs
            </a>
        </div>
    </div>
</div>


@endsection