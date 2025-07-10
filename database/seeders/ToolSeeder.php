<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Tool;
use App\Models\Goal;

class ToolSeeder extends Seeder
{
    public function run(): void
    {
        $tools = [
            [
                'name' => 'Email',
                'content_prompt' => 'Max 200 words',
                'budget' => 0,
                'deadline' => 2,
                'goals' => ['Inform']
            ],
            [
                'name' => 'Intranet article',
                'content_prompt' => 'Minimal 600 words, max 1200 words',
                'budget' => 0,
                'deadline' => 3,
                'goals' => ['Inform']
            ],
            [
                'name' => 'Blog post',
                'content_prompt' => 'Minimal 600 words, max 1200 words',
                'budget' => 0,
                'deadline' => 5,
                'goals' => ['Activate', 'Event']
            ],
            [
                'name' => 'Press release',
                'content_prompt' => 'Minimal 600 words, max 1200 words',
                'budget' => 0,
                'deadline' => 5,
                'goals' => ['Inform']
            ],
            [
                'name' => 'Facebook ad',
                'content_prompt' => 'Text (primary): ≤ 125 characters, Headline: ~3–6 words',
                'budget' => 200,
                'deadline' => 7,
                'goals' => ['Inform', 'Activate', 'Event']
            ],
            [
                'name' => 'Instagram ad',
                'content_prompt' => 'Caption: ~5–19 words (short, punchy), Optional image or video',
                'budget' => 200,
                'deadline' => 7,
                'goals' => ['Inform', 'Activate', 'Event']
            ],
            [
                'name' => 'LinkedIn ad',
                'content_prompt' => 'Primary text: ~20–50 words, Headline: ~5–10 words',
                'budget' => 200,
                'deadline' => 7,
                'goals' => ['Inform', 'Activate', 'Event']
            ],
            [
                'name' => 'Facebook post',
                'content_prompt' => 'Organic post: ~15–40 words (short, conversational)',
                'budget' => 0,
                'deadline' => 2,
                'goals' => ['Inform', 'Activate', 'Event']
            ],
            [
                'name' => 'Instagram post',
                'content_prompt' => 'Caption: ~100–150 characters. Use line breaks and emojis.',
                'budget' => 0,
                'deadline' => 2,
                'goals' => ['Inform', 'Activate', 'Event']
            ],
            [
                'name' => 'LinkedIn post',
                'content_prompt' => 'Text: ~50–150 words. Structure: hook → body → CTA.',
                'budget' => 0,
                'deadline' => 2,
                'goals' => ['Inform', 'Activate', 'Event']
            ],
            [
                'name' => 'Stakeholder update',
                'content_prompt' => '• Length: ~300–500 words• Structure: intro (purpose), key developments/milestones, next steps, summary',
                'budget' => 0,
                'deadline' => 4,
                'goals' => ['Inform']
            ],
            [
                'name' => 'Briefing for print company',
                'content_prompt' => '• 1–2 pages• Include: overview, goals, specs (size, materials, quantity), timeline, brand guidelines, delivery format',
                'budget' => 600,
                'deadline' => 7,
                'goals' => ['Inform']
            ],
            [
                'name' => 'Briefing for graphic designer',
                'content_prompt' => '• 1–2 pages• Include: project summary, objectives, target audience, tone/style, deliverables, spec sheet (sizes, formats), brand assets, due date',
                'budget' => 1200,
                'deadline' => 7,
                'goals' => ['Inform']
            ],
            [
                'name' => 'Briefing for video editor',
                'content_prompt' => '• 1–2 pages• Include: concept, script/reference, visuals, length, style, format/resolution, voice-over/music, deadlines, revisions',
                'budget' => 2000,
                'deadline' => 7,
                'goals' => ['Inform']
            ],
            [
                'name' => 'Briefing for event agency',
                'content_prompt' => '• 2–3 pages• Include: event objective, audience size/profile, key messages, branding, format, venue/logistics needs, budget, timeline, deliverables, contacts',
                'budget' => 8000,
                'deadline' => 7,
                'goals' => ['Inform','Event']
            ],
        ];

        foreach ($tools as $toolData) {
            $tool = Tool::create([
                'name' => $toolData['name'],
                'content_prompt' => $toolData['content_prompt'],
                'budget' => $toolData['budget'],
                'deadline' => $toolData['deadline'],
            ]);

            // Attach goal IDs using their names
            $goalIds = Goal::whereIn('name', $toolData['goals'])->pluck('id')->toArray();
            $tool->goals()->attach($goalIds);
        }
    }
}

