<?php

namespace Database\Seeders;

use App\Models\Testimonial;
use Illuminate\Database\Seeder;

class TestimonialSeeder extends Seeder
{
    public function run(): void
    {
        $testimonials = [
            [
                'name' => 'Ode',
                'role' => 'Business Owner',
                'avatar_initial' => 'O',
                'text' => 'When it comes to WordPress design, the expertise lies in cutting-edge visually stunning websites. Softorio is committed to ensuring quality improvements, promptly addressing any queries or concerns that may arise.',
            ],
            [
                'name' => 'Sarah M.',
                'role' => 'eCommerce Director',
                'avatar_initial' => 'S',
                'text' => "Softorio transformed our online presence completely. Their team delivered beyond expectations — fast, professional, and highly responsive. Our website's performance improved drastically after their optimization work.",
            ],
            [
                'name' => 'James K.',
                'role' => 'CTO, Tech Startup',
                'avatar_initial' => 'J',
                'text' => "The cloud infrastructure setup was seamless. Softorio handled everything from server configuration to security hardening. We haven't experienced a single moment of downtime in 2 years.",
            ],
            [
                'name' => 'Rachel T.',
                'role' => 'Marketing Manager',
                'avatar_initial' => 'R',
                'text' => 'Their SEO work brought us from page 5 to page 1 on Google within 4 months. The team is knowledgeable, transparent, and delivers real, measurable results.',
            ],
            [
                'name' => 'Michael P.',
                'role' => 'Operations Lead',
                'avatar_initial' => 'M',
                'text' => 'Working with Softorio on our automation project was a game-changer. They streamlined our entire workflow, saving us 20+ hours per week. Their technical expertise is unmatched.',
            ],
            [
                'name' => 'Lisa Chen',
                'role' => 'Founder, Online Retail',
                'avatar_initial' => 'L',
                'text' => "We migrated to a new eCommerce platform with Softorio's help. The process was smooth with zero downtime, and they even improved our loading speeds. Sales increased 35% in the first month.",
            ],
        ];

        foreach ($testimonials as $i => $testimonial) {
            Testimonial::updateOrCreate(
                ['name' => $testimonial['name'], 'role' => $testimonial['role']],
                $testimonial + ['stars' => 5, 'featured' => true, 'sort_order' => $i + 1]
            );
        }
    }
}
