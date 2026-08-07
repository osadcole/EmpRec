<?php

namespace App\DataFixtures;

use App\Entity\Employee;
use App\Entity\Account;
use App\Entity\Role;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $rolesData = [
            ['id' => 1, 'title' => 'DevOps Engineer', 'description' => 'Bring process improvements, tools, and methodologies to the software development lifecycle to increase efficiency and get software from concept to release quickly.', 'is_visible' => true],
            ['id' => 2, 'title' => 'Security Engineer', 'description' => null, 'is_visible' => true],
            ['id' => 3, 'title' => 'Full stack Developer', 'description' => 'Work on both the front-end and the back-end of an application.', 'is_visible' => true],
            ['id' => 4, 'title' => 'Quality Assurance Engineer', 'description' => 'Monitors every phase of the software development process, including design, development, testing, debugging and delivery.', 'is_visible' => true],
            ['id' => 5, 'title' => 'Mobile Developer', 'description' => ' Focus on developing apps for mobile devices', 'is_visible' => true],
        ];

        $rolesMap = [];
        foreach ($rolesData as $data) {
            $role = new Role();
            $role->setTitle($data['title'])
                ->setDescription($data['description'])
                ->setIsVisible($data['is_visible']);

            $manager->persist($role);
            $rolesMap[$data['id']] = $role;
        }

        $employeeRoleMap = [
            3 => [1, 2],
            4 => [3],
            5 => [4],
            6 => [5],
            7 => [3],
            11 => [5],
            18 => [5, 2, 3, 4],
            19 => [4, 5, 2]
        ];

        $employeesData = [
            ['id' => 3, 'name' => 'Jack', 'surname' => 'Burton', 'email' => 'jack-burton@mail.com', 'phone' => '+420111222333', 'web' => 'https://jack-burton.com', 'room' => 'T9:348', 'description' => "Jack is a skilled DevOps Engineer with experience in cloud infrastructure and automation.\n                       He focuses on streamlining deployment pipelines, improving system reliability, and supporting development teams.\n                       Outside of work, he enjoys experimenting with new technologies and sharing knowledge with colleagues.", 'photo' => 'images/jack-burton.jpg', 'is_active' => true],
            ['id' => 4, 'name' => 'Oliver', 'surname' => 'Dawson', 'email' => 'oliver-dawson@mail.com', 'phone' => '+420111111111', 'web' => 'https://oliver-dawson.com', 'room' => null, 'description' => null, 'photo' => 'images/oliver-dawson.jpg', 'is_active' => true],
            ['id' => 5, 'name' => 'Laurence', 'surname' => 'Welsh', 'email' => 'laurence-welsh@mail.com', 'phone' => '+420222222222', 'web' => null, 'room' => 'TH:A-942', 'description' => null, 'photo' => 'images/laurence-welsh.jpg', 'is_active' => true],
            ['id' => 6, 'name' => 'Jude', 'surname' => 'Goodman', 'email' => 'jude-goodman@mail.com', 'phone' => '+420333333333', 'web' => 'https://jude-goodman.com', 'room' => null, 'description' => "Jude Goodman's Description", 'photo' => 'images/jude-goodman.jpg', 'is_active' => true],
            ['id' => 7, 'name' => 'Daniel', 'surname' => 'Brown', 'email' => 'daniel-brown@mail.com', 'phone' => null, 'web' => 'https://daniel-brown.com', 'room' => 'T9:348', 'description' => "Daniel Brown's Description", 'photo' => null, 'is_active' => true],
            ['id' => 11, 'name' => 'James', 'surname' => 'White', 'email' => 'james-white@mail.com', 'phone' => '+420444444444', 'web' => null, 'room' => 'T9:350', 'description' => null, 'photo' => null, 'is_active' => true],
            ['id' => 18, 'name' => 'Jordan', 'surname' => 'Whales', 'email' => 'jordan.whales@example.com', 'phone' => '+420123456789', 'web' => 'https://jordan.whales.com', 'room' => 'T9:348', 'description' => "Some description", 'photo' => 'images/default-profile-image.jpg', 'is_active' => true],
            ['id' => 19, 'name' => 'Someone', 'surname' => 'VeryNew', 'email' => 'jordan.whales@example.com', 'phone' => '+420123456789', 'web' => 'https://jordan.whales.com', 'room' => 'T9:348', 'description' => "Some description", 'photo' => 'images/default-profile-image.jpg', 'is_active' => true],
        ];

        $employeesMap = [];
        foreach ($employeesData as $data) {
            $employee = new Employee();
            $employee->setName($data['name'])
                ->setSurname($data['surname'])
                ->setEmail($data['email'])
                ->setPhone($data['phone'])
                ->setWeb($data['web'])
                ->setRoom($data['room'])
                ->setDescription($data['description'])
                ->setPhoto($data['photo']);
            if (method_exists($employee, 'setIsActive')) {
                $employee->setIsActive($data['is_active']);
            }

            if (isset($employeeRoleMap[$data['id']])) {
                foreach ($employeeRoleMap[$data['id']] as $roleId) {
                    if (isset($rolesMap[$roleId])) {
                        $employee->addRole($rolesMap[$roleId]);
                    }
                }
            }

            $manager->persist($employee);
            $employeesMap[$data['id']] = $employee;
        }

        $accountsData = [
            ['employee_id' => 3, 'name' => 'Usermap', 'username' => 'jackburt', 'password' => 'OJIO!H*Y(*HB!JG*&!#GO', 'valid_to' => '2026-09-01 00:00:00.000'],
            ['employee_id' => 3, 'name' => 'LinkedIn', 'username' => 'jackburt', 'password' => 'ML:HUOH!*(Y)HO', 'valid_to' => null],
            ['employee_id' => 3, 'name' => 'Slack', 'username' => 'jackburt', 'password' => 'LHLKUIOU*OU*IL', 'valid_to' => '2027-09-01 00:00:00.000'],
            ['employee_id' => 4, 'name' => 'GitHub', 'username' => 'olivdaws', 'password' => 'm!JKHU*N!&*HNH&*h78h7678', 'valid_to' => '2028-04-22 00:00:00.000'],
            ['employee_id' => 5, 'name' => 'Figma', 'username' => 'laurewel', 'password' => 'MLNOIYUYohuty178ui', 'valid_to' => '2026-09-01 00:00:00.000'],
            ['employee_id' => 5, 'name' => 'Docker', 'username' => 'laurewel', 'password' => 'MLNOIYUYohuty178ui', 'valid_to' => '2026-06-26 00:00:00.000'],
            ['employee_id' => 6, 'name' => 'LinkedIn', 'username' => 'judegood', 'password' => 'IYO^O!*U^I!jioy6y7t7858', 'valid_to' => null],
            ['employee_id' => 7, 'name' => 'GitLab', 'username' => 'danbrown', 'password' => 'MKOK*&Y!HGBJKTIUO%&I', 'valid_to' => '2026-03-12 00:00:00.000'],
            ['employee_id' => 18, 'name' => 'test', 'username' => 'testusername', 'password' => 'eq\';\m21\'tlmoiwnqoqp', 'valid_to' => null],
            ['employee_id' => 18, 'name' => 'JordanWhalesAccount', 'username' => 'whalesjord', 'password' => 'lmkhOINOPh;jbouHGHIJL', 'valid_to' => '2026-09-01 00:00:00'],
            ['employee_id' => 18, 'name' => 'JordanWhalesAccount', 'username' => 'whalesjord', 'password' => 'lmkhOINOPh;jbouHGHIJL', 'valid_to' => '2026-09-01 00:00:00'],
            ['employee_id' => 19, 'name' => 'testAcc', 'username' => 'testusername', 'password' => 'eq\';\m21\'tlmoiwnqoqp', 'valid_to' => null],
        ];

        foreach ($accountsData as $data) {
            if (!isset($employeesMap[$data['employee_id']])) {
                continue;
            }

            $account = new Account();
            $account->setName($data['name'])
                ->setUsername($data['username'])
                ->setPassword($data['password'])
                ->setEmployee($employeesMap[$data['employee_id']]);

            if ($data['valid_to'] !== null) {
                $account->setValidTo(new \DateTime($data['valid_to']));
            } else {
                $account->setValidTo(null);
            }

            $manager->persist($account);
        }

        $manager->flush();
    }
}
