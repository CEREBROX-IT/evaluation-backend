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
            'Write your suggestions to improve the teacher and the class.',
        ];

        $teacherQuestions = ['Does institutional services outside of teaching (registration)', 'Does institutional services outside of teaching (committee tasks)', 'Communicates clearly in written and spoken words', 'Observes proper channel of communication and authority', 'Handles disagreement with composure and wisdom', 'Exercises his/her rights and accepts rights of others to participate in collective decisions', 'Tends to be flexible and open-minded, welcomes criticism and suggestions', 'Inspires feelings of friendliness and teamwork in an institutional task', 'Grooms himself/herself as a Christian professional (rated by superior and self only)', 'Advising', 'Comes to class regularly', 'Starts and ends classes punctually', 'Makes optimum use of the class hour', 'Conducts make up classes whenever necessary', 'Observes official consultation hour', 'Make oneself available to students for completion of grades'];

        $teacherQuestions2 = ['Observes/respects school policies (wears uniform regularly)', 'Observes/respects school policies (attends HSF meetings)', 'Observes/respects school policies (participates in school activitie)', 'Accepts assigned tasks with eager readiness', 'Attends institutional meetings regularly and punctually', 'Behaves in accordance with professional standards', 'Participates in community extension services', 'Is involved in church activities', 'Submits grade sheets, reports and other requirements on time', 'Inform superior of possible absences', 'Prepares/Uses instructional Materials (Module)', 'Prepares/Uses instructional Materials (CIDAM)', 'Prepares/Uses instructional Materials (Examinations)', 'Prepares/Uses instructional Materials (quiz notebooks/worksheets)'];

        $classroomQuestion1 = ['System and order in presenting the lesson', 'Pacing of Instruction'];

        $classroomQuestion2 = ['Use of instructional materials (Module,technology/audio/ visual aids, worksheets, rubric sheets, textbooks; etc)', 'Use of chalkboard/white board to make the lesson Understandable'];

        $classroomQuestion3 = ['Mastery of subject matter, both specific and related', 'Appropriateness of teaching strategy to the subject matter', 'Strategy used to encourage group/peer/tutoring/learning', 'Clarity in expressing thoughts and ideas', 'Modulation of voice', 'Enthusiasm in conducting the lesson', 'Ability to let most of the students participate in class activities', 'English/Filipino proficiency (pronunciation, grammar, and spelling)', 'Difficulty of questions appropriate to the level of students’ understanding', 'Levels of questions that elicit critical thinking', 'Re-explanation of misunderstood parts of the lesson', 'Ways of encouraging students to express their thoughts', 'Engagement of students in learning tasks', 'Observance of wait time for students to process their thoughts', 'Values Integration/IFL'];

        $classroomQuestion4 = ['Conditions for application of concepts taught', 'Monitoring of students’ understandings through formative assessment', 'Evaluation whether objectives set are attained or not', 'Interface (connecting areas that contribute to classroom climate)', 'Grooming of the teacher', 'Grooming of the students', 'Observance of classroom policies', 'The teacher’s calmness in interacting with students', 'The teacher’s warmth in dealing with students', 'The teacher’s proximity to students', 'The teacher’s care in interacting with erring students', 'The teacher’s sincerity about the learning of students', 'Freedom of students to interact in the classroom', 'Respect of students to the teacher', 'Order in classroom management'];

      
        
        $AdminQuestion2 = ['Decides quickly without conscious thought since he/she bases his/her decision on years of experience and practices', 'Discusses problems openly with colleagues who hold different ideas and opinions', 'Concentrates on listening to what the faculty express about themselves', 'Shows strong confidence in the correctness of his decisions based on his long years of experience as an administrator', 'Enjoys developing strategies that have a lasting impact on the institution', 'Shows innovative activities which are worthy of consideration in relation to school improvement', 'Has an overall view of the condition of the organization in an instant flash', 'Sets high standards of performance of faculty for greater results', 'Allows the faculty the freedom to develop their own projects because of trust', 'Personally acquaints himself/herself with the problems of each department under him.'];

        $AdminQuestion3 = ['Recognizes his/her own strengths as assets', 'Fully recognizes his/her own weaknesses and compensates', 'Projects a strong image before the faculty', 'Shows intellectual maturity in providing a well-balanced judgment or decision', 'Demonstrates a healthy personal relationship with others', 'Does not blame others for his wrong acts', 'Does not make alibis for any wrong act done by himself', 'Accepts criticism by weighing them gratefully as an opportunity to improve', 'Admits his/her mistakes', 'Acknowledges his fears instead of denying them', 'Deals with emergencies with clear thinking', 'Maintains that self-examination is a good starting point towards self-understanding'];

        $AdminQuestion4 = ['Considers the professional values of the faculty', 'Concerns himself/herself with the personal needs of the faculty', 'Exhibits fairness, consideration, and courtesy in dealing with the faculty', 'Provides the faculty security of employment based on the working policy', 'Encourages professional career advancement for growth and development', 'Allocates resources to the faculty for them to be innovative', 'Evaluates periodically the working performance of the faculty', 'Tolerates differences in opinion or viewpoint', 'Shows respect and consideration to students, parents, and the public in general', 'Maintains a clear, fair, and documented monitoring system', 'Shows the role of a good model according to the standards of the Seventh-day Adventist Church'];

        $AdminQuestion5 = ['Adopts a challenging new vision of what is desirable for the Institution', 'Provides a clear description of what the institution will evolve in the future', 'Motivates the faculty to perform above the routine contribution', 'Makes implementation results satisfactorily consistent with the original plan', 'Works well with the faculty to help them put into words their vision for the future', 'Simplifies ideas using easily understood language', 'Expresses himself clearly and concisely', 'Assures the faculty that their visions for the institution are vital in determining its direction', 'Supports the efforts of the faculty in team play', 'Strengthens the bond of loyalty among members of the school community', 'Encourages faculty creativity in meeting their professional needs', 'Provides sufficient time for assimilation and adjustment before implementing new or revised policies or procedures'];

        $AdminQuestion6 = ['Your personal life', 'The rate of progress of the school', 'The direction of the institution and denominational work as a whole'];

        $nonTeachingQuestion1 = ['Reports to work on time', 'Plans and requests time off for leaves ahead of time ', 'Fulfills agreed upon work-study schedule'];

        $nonTeachingQuestion2 = ['Is neat and clean', 'Dresses appropriately according to the nature of work'];

        $nonTeachingQuestion3 = ['Is able to organize work to maximize output', 'Is able to stay focus'];

        $nonTeachingQuestion4 = ['Is self-motivated', 'Actively seeks ways to gain progress', 'Is open to new ideas and approaches'];

        $nonTeachingQuestion5 = ['Uses tact and diplomacy', 'Shares information willingly', 'Committed to team success'];

        $nonTeachingQuestion6 = ['Fulfills job requirement as defined', 'Completes work on schedule', 'Is resourceful', 'Is able to work without undue supervision', 'Completes assigned tasks even under abnormal situations'];

        $nonTeachingQuestion7 = ['Makes rational decisions', 'Is able to solve work-related problems', 'Asks for support and help as needed'];

        $nonTeachingQuestion8 = ['Is willing to carry out responsibilities beyond the call of duty', 'Has a desire to improve performance', 'Welcomes corrections objectively and good-naturedly'];

        $nonTeachingQuestion9 = ['Practices economy in the use of resources/materials', 'Follows instructions to specific assigned task', 'Work output is free from errors'];

        $nonTeachingQuestion10 = ['Attends devotional meetings', 'Demonstrates Christian lifestyle and values based on SDA standards/principles'];

        $customerQuestion1 = ['Services available during office hours', 'Sufficient time is given to every client', 'Staffs are available to care for clients'];

        $customerQuestion2 = ['Awareness of clients presence and need', 'Responds to client’s need in a timely manner', 'Staffs are available to offer client’s assistance', 'Willing to extend services beyond office hours'];

        $customerQuestion3 = ['Cordial in handling client services', 'Tactful and patient in handling unpleasant matters', 'Visible culture of client care is prevalent'];

        $adminQuestion1 = ['Encourages the faculty in setting organizational goals and objectives', 'Provides the faculty with sufficient training to carry out their responsibilities well', 'Delegates responsibility effectively for greater results', 'Integrates planning activities at all levels', 'Shares as much information as possible with the faculty', 'Provides a collaborative environment that stresses a spirit of involvement', 'Shows confidence in the faculty to perform important tasks', 'Provides an environment of spirit of shared ownership', 'Allows the faculty to design a solution to their problem', 'Recognizes the success of the faculty by sharing in their joys', 'Makes the faculty feel that learning is necessary in achieving competence', 'Takes action only after sufficient planning and consultation', 'Involves and informs individuals or groups of the criterion or revision of a policy or procedure affecting them'];

        foreach ($questions as $description) {
            Question::create([
                'type' => 'STUDENT EVALUATION OF TEACHING',
                'question_group' => 'N/a',
                'evaluation_type' => 'General',
                'question_description' => $description,
                'status' => 1,
            ]);
        }

        foreach ($teacherQuestions as $teacherDescription) {
            Question::create([
                'type' => 'VALUATION OF TEACHERS PERFORMANCE',
                'question_group' => 'N/a',
                'evaluation_type' => 'People Effectiveness',
                'question_description' => $teacherDescription,
                'status' => 1,
            ]);
        }

        foreach ($teacherQuestions2 as $teacherDescription2) {
            Question::create([
                'type' => 'VALUATION OF TEACHERS PERFORMANCE',
                'question_group' => 'N/a',
                'evaluation_type' => 'Organizational Effectiveness',
                'question_description' => $teacherDescription2,
                'status' => 1,
            ]);
        }

        foreach ($classroomQuestion1 as $classroomDescription1) {
            Question::create([
                'type' => 'CLASSROOM OBSERVATION INSTRUMENT',
                'question_group' => 'N/a',
                'evaluation_type' => 'Organization',
                'question_description' => $classroomDescription1,
                'status' => 1,
            ]);
        }

        foreach ($classroomQuestion2 as $classroomDescription2) {
            Question::create([
                'type' => 'CLASSROOM OBSERVATION INSTRUMENT',
                'question_group' => 'N/a',
                'evaluation_type' => 'Materials',
                'question_description' => $classroomDescription2,
                'status' => 1,
            ]);
        }

        foreach ($classroomQuestion3 as $classroomDescription3) {
            Question::create([
                'type' => 'CLASSROOM OBSERVATION INSTRUMENT',
                'question_group' => 'N/a',
                'evaluation_type' => 'Instructional Process',
                'question_description' => $classroomDescription3,
                'status' => 1,
            ]);
        }

        foreach ($classroomQuestion4 as $classroomDescription4) {
            Question::create([
                'type' => 'CLASSROOM OBSERVATION INSTRUMENT',
                'question_group' => 'N/a',
                'evaluation_type' => 'Assessment',
                'question_description' => $classroomDescription4,
                'status' => 1,
            ]);
        }

        foreach ($adminQuestion1 as $adminDescription1) {
            Question::create([
                'type' => 'ADMINISTRATORS EVALUATION',
                'question_group' => '2',
                'evaluation_type' => 'Empowerment Skills',
                'question_description' => $adminDescription1,
                'status' => 1,
            ]);
        }

        foreach ($AdminQuestion2 as $adminDescription2) {
            Question::create([
                'type' => 'ADMINISTRATORS EVALUATION',
                'question_group' => '2',
                'evaluation_type' => 'Intuition Skill',
                'question_description' => $adminDescription2,
                'status' => 1,
            ]);
        }

        foreach ($AdminQuestion3 as $adminDescription3) {
            Question::create([
                'type' => 'ADMINISTRATORS EVALUATION',
                'question_group' => '2',
                'evaluation_type' => 'Self-Understanding Skill',
                'question_description' => $adminDescription3,
                'status' => 1,
            ]);
        }

        foreach ($AdminQuestion4 as $adminDescription4) {
            Question::create([
                'type' => 'ADMINISTRATORS EVALUATION',
                'question_group' => '2',
                'evaluation_type' => 'Value Congruence Skill',
                'question_description' => $adminDescription4,
                'status' => 1,
            ]);
        }

        foreach ($AdminQuestion5 as $adminDescription5) {
            Question::create([
                'type' => 'ADMINISTRATORS EVALUATION',
                'question_group' => '2',
                'evaluation_type' => 'Vision Skill',
                'question_description' => $adminDescription5,
                'status' => 1,
            ]);
        }

        foreach ($AdminQuestion6 as $adminDescription6) {
            Question::create([
                'type' => 'ADMINISTRATORS EVALUATION',
                'question_group' => '3',
                'evaluation_type' => 'General',
                'question_description' => $adminDescription6,
                'status' => 1,
            ]);
        }

        Question::create([
            'type' => 'ADMINISTRATORS EVALUATION',
            'question_group' => '4',
            'evaluation_type' => 'General',
            'question_description' => 'How do you classify the type of administration exhibited by the administrator previously specified?',
            'status' => 1,
        ]);

        foreach ($nonTeachingQuestion1 as $nonTeacingDescription1) {
            Question::create([
                'type' => 'EVALUATION INSTRUMENT FOR NON-TEACHING',
                'question_group' => 'N/a',
                'evaluation_type' => 'Attendance and Punctuality',
                'question_description' => $nonTeacingDescription1,
                'status' => 1,
            ]);
        }

        foreach ($nonTeachingQuestion2 as $nonTeacingDescription2) {
            Question::create([
                'type' => 'EVALUATION INSTRUMENT FOR NON-TEACHING',
                'question_group' => 'N/a',
                'evaluation_type' => 'Appearance',
                'question_description' => $nonTeacingDescription2,
                'status' => 1,
            ]);
        }

        foreach ($nonTeachingQuestion3 as $nonTeachingDescription3) {
            Question::create([
                'type' => 'EVALUATION INSTRUMENT FOR NON-TEACHING',
                'question_group' => 'N/a',
                'evaluation_type' => 'Organizational skills',
                'question_description' => $nonTeachingDescription3,
                'status' => 1,
            ]);
        }

        foreach ($nonTeachingQuestion4 as $nonTeachingDescription4) {
            Question::create([
                'type' => 'EVALUATION INSTRUMENT FOR NON-TEACHING',
                'question_group' => 'N/a',
                'evaluation_type' => 'Initiative',
                'question_description' => $nonTeachingDescription4,
                'status' => 1,
            ]);
        }

        foreach ($nonTeachingQuestion5 as $nonTeachingDescription5) {
            Question::create([
                'type' => 'EVALUATION INSTRUMENT FOR NON-TEACHING',
                'question_group' => 'N/a',
                'evaluation_type' => 'Interpersonal Skills',
                'question_description' => $nonTeachingDescription5,
                'status' => 1,
            ]);
        }

        foreach ($nonTeachingQuestion6 as $nonTeachingDescription6) {
            Question::create([
                'type' => 'EVALUATION INSTRUMENT FOR NON-TEACHING',
                'question_group' => 'N/a',
                'evaluation_type' => 'Work Ethics',
                'question_description' => $nonTeachingDescription6,
                'status' => 1,
            ]);
        }

        // Question 7
        foreach ($nonTeachingQuestion7 as $nonTeachingDescription7) {
            Question::create([
                'type' => 'EVALUATION INSTRUMENT FOR NON-TEACHING',
                'question_group' => 'N/a',
                'evaluation_type' => 'Judgment',
                'question_description' => $nonTeachingDescription7,
                'status' => 1,
            ]);
        }
        foreach ($nonTeachingQuestion8 as $nonTeachingDescription8) {
            Question::create([
                'type' => 'EVALUATION INSTRUMENT FOR NON-TEACHING',
                'question_group' => 'N/a',
                'evaluation_type' => 'Attitude',
                'question_description' => $nonTeachingDescription8,
                'status' => 1,
            ]);
        }

        foreach ($nonTeachingQuestion9 as $nonTeachingDescription9) {
            Question::create([
                'type' => 'EVALUATION INSTRUMENT FOR NON-TEACHING',
                'question_group' => 'N/a',
                'evaluation_type' => 'Output',
                'question_description' => $nonTeachingDescription9,
                'status' => 1,
            ]);
        }

        foreach ($nonTeachingQuestion10 as $nonTeachingDescription10) {
            Question::create([
                'type' => 'EVALUATION INSTRUMENT FOR NON-TEACHING',
                'question_group' => 'N/a',
                'evaluation_type' => 'Religiosity',
                'question_description' => $nonTeachingDescription10,
                'status' => 1,
            ]);
        }

        foreach ($customerQuestion1 as $customerDescription1) {
            Question::create([
                'type' => 'EVALUATION INSTRUMENT FOR CUSTOMER',
                'question_group' => 'N/a',
                'evaluation_type' => 'Availability',
                'question_description' => $customerDescription1,
                'status' => 1,
            ]);
        }

        foreach ($customerQuestion2 as $customerDescription2) {
            Question::create([
                'type' => 'EVALUATION INSTRUMENT FOR CUSTOMER',
                'question_group' => 'N/a',
                'evaluation_type' => 'Client Services',
                'question_description' => $customerDescription2,
                'status' => 1,
            ]);
        }

        foreach ($customerQuestion3 as $customerDescription3) {
            Question::create([
                'type' => 'EVALUATION INSTRUMENT FOR CUSTOMER',
                'question_group' => 'N/a',
                'evaluation_type' => 'Client Relations',
                'question_description' => $customerDescription3,
                'status' => 1,
            ]);
        }
    }
}
