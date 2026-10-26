<?php

namespace Database\Seeders;

use App\Models\TeamMember;
use Illuminate\Database\Seeder;

class TeamMemberSeeder extends Seeder
{
    public function run(): void
    {
        $members = [
            [
                'name' => 'Md. Arif Billah',
                'role' => 'Founder & Chief Executive Officer',
                'short_role' => 'Founder & CEO',
                'bio' => "Arif founded Softorio in 2021 while studying Computer Science & Engineering — turning years of freelancing into a full-service digital agency serving international clients. He also founded Hostorio, a web hosting company providing shared, business, reseller and VPS hosting.\n\nA cloud and systems specialist, he architects infrastructure on AWS, manages Linux servers (Ubuntu & AlmaLinux), and leads WordPress development end to end. He's a Fiverr Level 2 Seller, an Upwork Top Rated freelancer, and an AWS-certified engineer.",
                'photo_path' => 'images/arif.png',
                'email' => 'contact@arifbillah.com',
                'website_url' => 'https://arifbillah.com',
                'credentials_heading' => 'Recognition & Roles',
                'credentials' => [
                    '1st Runner Up — Hult Prize On-Campus Competition',
                    'Membership Development Coordinator — IEEE Computer Society, GUB',
                    'AWS Certified — BASIS Institute of Technology & Management',
                    'BSc in Computer Science & Engineering — Green University of Bangladesh',
                ],
                'skills' => ['AWS Cloud', 'Linux Server Admin', 'WordPress', 'PHP', 'DevOps', 'cPanel / WHM', 'DNS & SSL/TLS', 'Web Hosting'],
                'stats' => [
                    ['value' => 'B.Sc. CSE', 'label' => 'Computer Science & Engineering'],
                    ['value' => '300+', 'label' => 'Websites Delivered'],
                    ['value' => 'Top Rated', 'label' => 'Upwork Freelancer'],
                    ['value' => 'Level 2', 'label' => 'Fiverr Seller'],
                ],
                'sort_order' => 1,
            ],
            [
                'name' => 'Asma Akter',
                'role' => 'Co-Founder & Head of Research',
                'short_role' => 'Co-Founder',
                'bio' => "Asma co-leads Softorio with a focus on research, quality, and emerging technology. A Computer Science & Engineering engineer from Green University of Bangladesh, she brings a strong analytical foundation in programming, databases, networking, and artificial intelligence.\n\nHer passion lies in cybersecurity and machine learning — particularly AI-based cyber threat detection and secure, intelligent systems. That research mindset shapes how Softorio approaches security, reliability, and innovation on every project.",
                'photo_path' => 'images/asma-formal.png',
                'email' => 'asmaakter3648@gmail.com',
                'website_url' => null,
                'credentials_heading' => 'Focus & Strengths',
                'credentials' => [
                    'Cybersecurity & AI-based cyber threat detection research',
                    'Web programming with PHP & MySQL, CRUD & file systems',
                    'Network design & simulation, Java socket programming',
                    'BSc in Computer Science & Engineering — Green University of Bangladesh',
                ],
                'skills' => ['Python', 'Java', 'C / C++', 'Cybersecurity', 'Machine Learning', 'PHP & MySQL', 'Networking', 'Data Analysis'],
                'stats' => [
                    ['value' => 'B.Sc. CSE', 'label' => 'Computer Science & Engineering'],
                    ['value' => '5.00', 'label' => 'SSC — Perfect GPA'],
                    ['value' => 'AI / ML', 'label' => 'Research Focus'],
                    ['value' => 'Security', 'label' => 'Cyber Threat Detection'],
                ],
                'sort_order' => 2,
            ],
        ];

        foreach ($members as $member) {
            TeamMember::updateOrCreate(['name' => $member['name']], $member);
        }
    }
}
