<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Question;

class QuestionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $questions = [
            'Has an interesting style of delivery.',
            'Has increased my interest in the subject.',
            'Has encouraged me to think.',
            'Is sensitive to how well the class makes progress.',
            'Is friendly and approachable.',
            'Has shown mastery in his/her subject matter.',
            'Prepares exams that reflect course content and objectives.',
            'Presents material in a clear, well-organized, and logical manner.',
            'Teaches the subject with enthusiasm.',
            'Helps me outside class when requested.',
            'Demonstrates by behavior, attitude, and relationships that he/she is a committed Christian.',
            'Is clear and understandable.',
            'Encourages questions, discussion and is open to others’ opinions.',
            'Utilizes group/peer tutoring/learning.',
            'Is fair in grading students.',
            'Integrates faith and learning.',
            'Gives reasonable amount of time necessary for preparations on requirements.',
            'Maximizes use of instructional materials (modules, technology/ audio/visual aids, worksheets, rubric sheets, textbooks, chalk boards/ whiteboard).',
            'Uses teaching strategies appropriate to the subject matter.',
            'Provides conditions/activities for application of concepts taught.',
            'Monitors students’ understanding of the subject matter.',
            'Imposes classroom policies (checking of attendance and uniform, Praying before start on class, requiring proper behavior).',
            'Write what you like/appreciate about the teacher and the class.',
            'Write your suggestions to improve the teacher and the class.'
        ];

      

            $teacherQuestions = [
                'Does institutional services outside of teaching (registration)',
                'Does institutional services outside of teaching (committee tasks)',
                'Communicates clearly in written and spoken words',
                'Observes proper channel of communication and authority',
                'Handles disagreement with composure and wisdom',
                'Exercises his/her rights and accepts rights of others to participate in collective decisions',
                'Tends to be flexible and open-minded, welcomes criticism and suggestions',
                'Inspires feelings of friendliness and teamwork in an institutional task',
                'Grooms himself/herself as a Christian professional (rated by superior and self only)',
                'Advising',
                'Comes to class regularly',
                'Starts and ends classes punctually',
                'Makes optimum use of the class hour',
                'Conducts make up classes whenever necessary',
                'Observes official consultation hour',
                'Make oneself available to students for completion of grades'
            ];
            
            $teacherQuestions2 = [
                'Observes/respects school policies (wears uniform regularly)',
                'Observes/respects school policies (attends HSF meetings)',
                'Observes/respects school policies (participates in school activitie)',

                'Accepts assigned tasks with eager readiness',
                'Attends institutional meetings regularly and punctually',
                'Behaves in accordance with professional standards',
                'Participates in community extension services',
                'Is involved in church activities',
                'Submits grade sheets, reports and other requirements on time',
                'Inform superior of possible absences',
                'Prepares/Uses instructional Materials (Module)',
                'Prepares/Uses instructional Materials (CIDAM)',
                'Prepares/Uses instructional Materials (Examinations)',
                'Prepares/Uses instructional Materials (quiz notebooks/worksheets)',



            ];
    

        foreach ($questions as $description) {
            Question::create([
                'evaluation_for' => 'Student',
                'evaluation_type' => 'General',
                'question_description' => $description,
                'status' => 1,
            ]);
        }

        foreach ($teacherQuestions as $teacherDescription) {
            Question::create([
                'evaluation_for' => 'Teacher',
                'evaluation_type' => 'People Effectiveness',
                'question_description' => $teacherDescription,
                'status' => 1,
            ]);

            
        }

        foreach ($teacherQuestions2 as $teacherDescription2) {
            Question::create([
                'evaluation_for' => 'Teacher',
                'evaluation_type' => 'Organizational Effectiveness',
                'question_description' => $teacherDescription2,
                'status' => 1,
            ]);
        }
    }
}