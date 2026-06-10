<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Student;
use App\Models\Instructor;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Creating admin account...');
        
        // Create Admin
        User::create([
            'name' => 'System Administrator',
            'username' => 'admin',
            'email' => 'admin@elearning.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'status' => 'active',
        ]);

        $this->command->info('Creating 50+ demo students...');
        $this->createStudents();

        $this->command->info('User seeding complete!');
    }

    private function createStudents(): void
    {
        $students = [
            ['name' => 'Alice Johnson', 'email' => 'student@elearning.com', 'gender' => 'female', 'phone' => '+250789123456', 'address' => 'Kicukiro District, Kigali, Rwanda'],
            ['name' => 'Bob Mugisha', 'email' => 'student2@elearning.com', 'gender' => 'male', 'phone' => '+250789654321', 'address' => 'Gasabo District, Kigali, Rwanda'],
            ['name' => 'Claire Uwimana', 'email' => 'claire.uwimana@elearning.com', 'gender' => 'female', 'phone' => '+250781234567', 'address' => 'Nyarugenge District, Kigali, Rwanda'],
            ['name' => 'David Habimana', 'email' => 'david.habimana@elearning.com', 'gender' => 'male', 'phone' => '+250782345678', 'address' => 'Huye District, Southern Province, Rwanda'],
            ['name' => 'Evelyne Mukamana', 'email' => 'evelyne.mukamana@elearning.com', 'gender' => 'female', 'phone' => '+250783456789', 'address' => 'Musanze District, Northern Province, Rwanda'],
            ['name' => 'Frank Niyonzima', 'email' => 'frank.niyonzima@elearning.com', 'gender' => 'male', 'phone' => '+250784567890', 'address' => 'Rubavu District, Western Province, Rwanda'],
            ['name' => 'Grace Uwase', 'email' => 'grace.uwase@elearning.com', 'gender' => 'female', 'phone' => '+250785678901', 'address' => 'Nyagatare District, Eastern Province, Rwanda'],
            ['name' => 'Hakizimana Jean', 'email' => 'hakizimana.jean@elearning.com', 'gender' => 'male', 'phone' => '+250786789012', 'address' => 'Ruhango District, Southern Province, Rwanda'],
            ['name' => 'Ishimwe Patrick', 'email' => 'ishimwe.patrick@elearning.com', 'gender' => 'male', 'phone' => '+250787890123', 'address' => 'Gicumbi District, Northern Province, Rwanda'],
            ['name' => 'Jeanne dArc', 'email' => 'jeanne.darc@elearning.com', 'gender' => 'female', 'phone' => '+250788901234', 'address' => 'Kamonyi District, Southern Province, Rwanda'],
            ['name' => 'Karake Emmanuel', 'email' => 'karake.emmanuel@elearning.com', 'gender' => 'male', 'phone' => '+250789012345', 'address' => 'Ngoma District, Eastern Province, Rwanda'],
            ['name' => 'Linda Mukeshimana', 'email' => 'linda.mukeshimana@elearning.com', 'gender' => 'female', 'phone' => '+250790123456', 'address' => 'Gakenke District, Northern Province, Rwanda'],
            ['name' => 'Manzi Olivier', 'email' => 'manzi.olivier@elearning.com', 'gender' => 'male', 'phone' => '+250791234567', 'address' => 'Nyanza District, Southern Province, Rwanda'],
            ['name' => 'Nadine Uwimana', 'email' => 'nadine.uwimana@elearning.com', 'gender' => 'female', 'phone' => '+250792345678', 'address' => 'Karongi District, Western Province, Rwanda'],
            ['name' => 'Olivier Mugabo', 'email' => 'olivier.mugabo@elearning.com', 'gender' => 'male', 'phone' => '+250793456789', 'address' => 'Rwamagana District, Eastern Province, Rwanda'],
            ['name' => 'Pacifique Niyitegeka', 'email' => 'pacifique.niyitegeka@elearning.com', 'gender' => 'male', 'phone' => '+250794567890', 'address' => 'Burera District, Northern Province, Rwanda'],
            ['name' => 'Queen Mukamana', 'email' => 'queen.mukamana@elearning.com', 'gender' => 'female', 'phone' => '+250795678901', 'address' => 'Nyabihu District, Western Province, Rwanda'],
            ['name' => 'Rene Hakizimana', 'email' => 'rene.hakizimana@elearning.com', 'gender' => 'male', 'phone' => '+250796789012', 'address' => 'Bugesera District, Eastern Province, Rwanda'],
            ['name' => 'Sandrine Uwase', 'email' => 'sandrine.uwase@elearning.com', 'gender' => 'female', 'phone' => '+250797890123', 'address' => 'Gatsibo District, Eastern Province, Rwanda'],
            ['name' => 'Theoneste Ndayisaba', 'email' => 'theoneste.ndayisaba@elearning.com', 'gender' => 'male', 'phone' => '+250798901234', 'address' => 'Nyamasheke District, Western Province, Rwanda'],
            ['name' => 'Ursule Nyiraneza', 'email' => 'ursule.nyiraneza@elearning.com', 'gender' => 'female', 'phone' => '+250799012345', 'address' => 'Rusizi District, Western Province, Rwanda'],
            ['name' => 'Valens Nshimiyimana', 'email' => 'valens.nshimiyimana@elearning.com', 'gender' => 'male', 'phone' => '+250700123456', 'address' => 'Kayonza District, Eastern Province, Rwanda'],
            ['name' => 'Winnie Mukamurenzi', 'email' => 'winnie.mukamurenzi@elearning.com', 'gender' => 'female', 'phone' => '+250701234567', 'address' => 'Ngororero District, Western Province, Rwanda'],
            ['name' => 'Yves Niyonzima', 'email' => 'yves.niyonzima@elearning.com', 'gender' => 'male', 'phone' => '+250702345678', 'address' => 'Kirundo Province, Burundi'],
            ['name' => 'Zahara Uwimana', 'email' => 'zahara.uwimana@elearning.com', 'gender' => 'female', 'phone' => '+250703456789', 'address' => 'Bujumbura, Burundi'],
            ['name' => 'Amos Ndayizeye', 'email' => 'amos.ndayizeye@elearning.com', 'gender' => 'male', 'phone' => '+250704567890', 'address' => 'Gitega Province, Burundi'],
            ['name' => 'Beatrice Nyiraneza', 'email' => 'beatrice.nyiraneza@elearning.com', 'gender' => 'female', 'phone' => '+250705678901', 'address' => 'Ngozi Province, Burundi'],
            ['name' => 'Celestin Hakizimana', 'email' => 'celestin.hakizimana@elearning.com', 'gender' => 'male', 'phone' => '+250706789012', 'address' => 'Muyinga Province, Burundi'],
            ['name' => 'Diane Mukeshimana', 'email' => 'diane.mukeshimana@elearning.com', 'gender' => 'female', 'phone' => '+250707890123', 'address' => 'Kayanza Province, Burundi'],
            ['name' => 'Eric Niyibizi', 'email' => 'eric.niyibizi@elearning.com', 'gender' => 'male', 'phone' => '+250708901234', 'address' => 'Butare, Southern Province, Rwanda'],
            ['name' => 'Fiona Uwineza', 'email' => 'fiona.uwineza@elearning.com', 'gender' => 'female', 'phone' => '+250709012345', 'address' => 'Kibuye, Western Province, Rwanda'],
            ['name' => 'Gaspard Mugisha', 'email' => 'gaspard.mugisha@elearning.com', 'gender' => 'male', 'phone' => '+250710123456', 'address' => 'Byumba, Northern Province, Rwanda'],
            ['name' => 'Honorine Uwimana', 'email' => 'honorine.uwimana@elearning.com', 'gender' => 'female', 'phone' => '+250711234567', 'address' => 'Kibungo, Eastern Province, Rwanda'],
            ['name' => 'Innocent Habimana', 'email' => 'innocent.habimana@elearning.com', 'gender' => 'male', 'phone' => '+250712345678', 'address' => 'Cyangugu, Western Province, Rwanda'],
            ['name' => 'Jacqueline Mukamana', 'email' => 'jacqueline.mukamana@elearning.com', 'gender' => 'female', 'phone' => '+250713456789', 'address' => 'Gisenyi, Western Province, Rwanda'],
            ['name' => 'Keziah Nishimwe', 'email' => 'keziah.nishimwe@elearning.com', 'gender' => 'female', 'phone' => '+250714567890', 'address' => 'Kabuga, Kigali, Rwanda'],
            ['name' => 'Lionel Niyonzima', 'email' => 'lionel.niyonzima@elearning.com', 'gender' => 'male', 'phone' => '+250715678901', 'address' => 'Remera, Kigali, Rwanda'],
            ['name' => 'Marie Rose', 'email' => 'marierose@elearning.com', 'gender' => 'female', 'phone' => '+250716789012', 'address' => 'Kacyiru, Kigali, Rwanda'],
            ['name' => 'Nathan Mugabo', 'email' => 'nathan.mugabo@elearning.com', 'gender' => 'male', 'phone' => '+250717890123', 'address' => 'Kimironko, Kigali, Rwanda'],
            ['name' => 'Olivia Mutuyimana', 'email' => 'olivia.mutuyimana@elearning.com', 'gender' => 'female', 'phone' => '+250718901234', 'address' => 'Nyamirambo, Kigali, Rwanda'],
            ['name' => 'Pierre Nkinzingabo', 'email' => 'pierre.nkinzingabo@elearning.com', 'gender' => 'male', 'phone' => '+250719012345', 'address' => 'Kanombe, Kigali, Rwanda'],
            ['name' => 'Rachel Uwimana', 'email' => 'rachel.uwimana@elearning.com', 'gender' => 'female', 'phone' => '+250720123456', 'address' => 'Gikondo, Kigali, Rwanda'],
            ['name' => 'Samuel Niyigena', 'email' => 'samuel.niyigena@elearning.com', 'gender' => 'male', 'phone' => '+250721234567', 'address' => 'Nyabugogo, Kigali, Rwanda'],
            ['name' => 'Tiffany Uwase', 'email' => 'tiffany.uwase@elearning.com', 'gender' => 'female', 'phone' => '+250722345678', 'address' => 'Kabeza, Kigali, Rwanda'],
            ['name' => 'Emmanuel Niyonzima', 'email' => 'emmanuel.niyonzima@elearning.com', 'gender' => 'male', 'phone' => '+250723456789', 'address' => 'Nyarutarama, Kigali, Rwanda'],
            ['name' => 'Sarah Johnson', 'email' => 'sarah.johnson@elearning.com', 'gender' => 'female', 'phone' => '+250724567890', 'address' => 'Kicukiro, Kigali, Rwanda'],
            ['name' => 'Michael Davis', 'email' => 'michael.davis@elearning.com', 'gender' => 'male', 'phone' => '+250725678901', 'address' => 'Kawangire, Eastern Province, Rwanda'],
            ['name' => 'Jessica Williams', 'email' => 'jessica.williams@elearning.com', 'gender' => 'female', 'phone' => '+250726789012', 'address' => 'Muhanga District, Southern Province, Rwanda'],
            ['name' => 'Daniel Bizimana', 'email' => 'daniel.bizimana@elearning.com', 'gender' => 'male', 'phone' => '+250727890123', 'address' => 'Rulindo District, Northern Province, Rwanda'],
            ['name' => 'Esther Uwitonze', 'email' => 'esther.uwitonze@elearning.com', 'gender' => 'female', 'phone' => '+250728901234', 'address' => 'Rutsiro District, Western Province, Rwanda'],
            ['name' => 'Felix Ntwari', 'email' => 'felix.ntwari@elearning.com', 'gender' => 'male', 'phone' => '+250729012345', 'address' => 'Kirehe District, Eastern Province, Rwanda'],
            ['name' => 'Grace Uwimana', 'email' => 'grace2.uwimana@elearning.com', 'gender' => 'female', 'phone' => '+250730123456', 'address' => 'Gisagara District, Southern Province, Rwanda'],
        ];

        foreach ($students as $data) {
            $user = User::create([
                'name' => $data['name'],
                'username' => Str::slug($data['name']) . '-' . Str::random(4),
                'email' => $data['email'],
                'password' => Hash::make('password'),
                'role' => 'student',
                'status' => 'active',
            ]);

            $studentNumber = 'STU-' . date('Y') . '-' . str_pad($user->id, 4, '0', STR_PAD_LEFT);

            Student::create([
                'user_id' => $user->id,
                'student_number' => $studentNumber,
                'name' => $data['name'],
                'email' => $data['email'],
                'phone' => $data['phone'],
                'gender' => $data['gender'],
                'address' => $data['address'],
                'points' => rand(50, 500),
                'level' => rand(1, 3),
            ]);
        }
    }
}
