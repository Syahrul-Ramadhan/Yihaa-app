@php
    $teams = $teams ?? [
        ['name' => 'YihaaTeam', 'members' => '6/7 Member'],
        ['name' => 'Avengers', 'members' => '8/10 Member'],
        ['name' => 'APALAH', 'members' => '3/5 Member'],
        ['name' => 'MahaHub', 'members' => '9/10 Member'],
        ['name' => 'Anjayanto', 'members' => '2/3 Member'],
        ['name' => 'VervalPat', 'members' => '10/15 Member'],
        ['name' => 'Adhesi', 'members' => '5/6 Member'],
        ['name' => 'YellowBaby', 'members' => '3/4 Member'],
        ['name' => 'Godzilla', 'members' => '7/10 Member'],
        ['name' => 'Phoenix', 'members' => '4/5 Member'],
    ];
@endphp

<div class="rounded-2xl p-6" style="background-color:#0E2F3E; box-shadow:0 4px 10px rgba(0, 224, 255, 0.15); color:#FFFFFF;">
    <h3 class="text-lg font-semibold mb-4" style="color:#00E0FF;">Team Recommendations</h3>
    <ol class="space-y-2">
        @foreach ($teams as $idx => $team)
            <li class="flex items-center justify-between py-2 px-3 rounded-lg">
                <span class="flex items-center gap-3">
                    <span class="w-5 text-right" style="color:#B0C7CC;">{{ $idx + 1 }}.</span>
                    <span style="color:#FFFFFF;">{{ $team['name'] }}</span>
                </span>
                <span class="text-sm" style="color:#B0C7CC;">{{ $team['members'] }}</span>
            </li>
        @endforeach
    </ol>
    <div class="text-center mt-4">
        <a href="#" class="font-medium" style="color:#00E0FF;">Show More</a>
    </div>
    </div>

