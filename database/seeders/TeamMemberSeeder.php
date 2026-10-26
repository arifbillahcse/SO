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
                'bio' => "Arif founded Softorio in 2021 while studying Computer Science & Engineering — turning years of freelancing into a full-service digital agency serving international clients. He also founded Hostorio, a web hosting company providing shared, business, reseller and VPS hosting.\n\nA cloud and systems specialist, he architects infrastructure on AWS, manages Linux servers (Ubuntu & AlmaLinux), and leads WordPress development end to end. He's a Fiverr Level 2 Seller, an Upwork Top Rated freelancer, and an AWS-certified engineer.",
                'photo_path' => 'images/arif.png',
                'credentials' => [
                    '1st Runner Up — Hult Prize On-Campus Competition',
                    'Membership Development Coordinator — IEEE Computer Society, GUB',
                    'AWS Certified — BASIS Institute of Technology & Management',
                    'BSc in Computer Science & Engineering — Green University of Bangladesh',
                ],
                'skills' => ['AWS Cloud', 'Linux Server Admin', 'WordPress', 'PHP', 'DevOps', 'cPanel / WHM', 'DNS & SSL/TLS', 'Web Hosting'],
                'sort_order' => 1,
            ],
            [
                'name' => 'Asma Akter',
                'role' => 'Co-Founder & Head of Research',
                'bio' => "Asma co-leads Softorio with a focus on research, quality, and emerging technology. A Computer Science & Engineering engineer from Green University of Bangladesh, she brings a strong analytical foundation in programming, databases, networking, and artificial intelligence.\n\nHer passion lies in cybersecurity and machine learning — particularly AI-based cyber threat detection and secure, intelligent systems. That research mindset shapes how Softorio approaches security, reliability, and innovation on every project.",
                'photo_path' => 'images/asma-formal.png',
                'credentials' => [
                    'Cybersecurity & AI-based cyber threat detection research',
                    'Web programming with PHP & MySQL, CRUD & file systems',
                    'Network design & simulation, Java socket programming',
                    'BSc in Computer Science & Engineering — Green University of Bangladesh',
                ],
                'skills' => ['Python', 'Java', 'C / C++', 'Cybersecurity', 'Machine Learning', 'PHP & MySQL', 'Networking', 'Data Analysis'],
                'sort_order' => 2,
            ],
        ];

        foreach ($members as $member) {
            TeamMember::updateOrCreate(['name' => $member['name']], $member);
        }
    }
}
