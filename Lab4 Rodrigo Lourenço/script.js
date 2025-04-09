// Ex 1: Create class Person with firstName, lastName, yearOfBirth
class Person {
    constructor(firstName, lastName, yearOfBirth) {
        this.firstName = firstName;
        this.lastName = lastName;
        this.yearOfBirth = yearOfBirth;
    }
    get fullName() {
        return `${this.firstName} ${this.lastName}`;
    }

    set yearOfBirth(year) {
        this._yearOfBirth = year;
    }

    get age() {
        const currentYear = new Date().getFullYear();  // Get current year
        return currentYear - this._yearOfBirth;  // Calculate age
    }

    printInfo() {
        console.log(`Name: ${this.fullName}, Age: ${this.age}, Year of Birth: ${this._yearOfBirth}`);
    }
}

// Ex 2: Create class Student extends Person
class Student extends Person {
    constructor(firstName, lastName, yearOfBirth, group, isErasmus, grades) {
        super(firstName, lastName, yearOfBirth);
        this.group = group;
        this.isErasmus = isErasmus;
        this.grades = grades;
    }

    printStudentInfo() {
        console.log(`Student: ${this.fullName}, Group: ${this.group}, Erasmus: ${this.isErasmus ? "Yes" : "No"}`);
    }

    calculateAverageGrades() {
        const total = this.grades.reduce((sum, grade) => sum + grade, 0);
        return total / this.grades.length;
    }
}

// Ex 3: Generate 10 random instances of students (names, grades, etc...), i´m going to use Slovak Names :)
function generateRandomStudents() {
    const groups = ["A", "B", "C", "D", "E"];
    const fnames = ["Ema", "Sofia", "Nina", "Viktória", "Natália", "Patrik", "Jakub", "Samuel", "Michal", "Adam"];  // First Names random, Slovak Names :)
    const lnames = ["Kovác", "Mlynár", "Baca", "Rybár", "Král", "Pekár", "Kuchár", "Masiar", "Maliar", "Hrkút"];  // Last Names random, Slovak names too :)

    const students = [];

    // Loop to generate 10 students
    for (let i = 0; i < 10; i++) {
        const firstName = fnames[Math.floor(Math.random() * fnames.length)];
        const lastName = lnames[Math.floor(Math.random() * lnames.length)];
        const yearOfBirth = 2000 + Math.floor(Math.random() * 10);
        const group = groups[Math.floor(Math.random() * groups.length)];
        const isErasmus = Math.random() > 0.5;
        const grades = Array.from({ length: 5 }, () => Math.floor(Math.random() * 5) + 1);  // Gen bet 1 and 5

        const student = new Student(firstName, lastName, yearOfBirth, group, isErasmus, grades);  // Create new student instance
        students.push(student);  // Add student to array
    }

    return students;
}

const students = generateRandomStudents();  // Generate the students

// Ex 4: Find student with best and worst avg
function findBestAndWorstStudent(students) {
    let bestStudent = students[0];
    let worstStudent = students[0];

    students.forEach(student => {
        if (student.calculateAverageGrades() > bestStudent.calculateAverageGrades()) {
            bestStudent = student; //upd best
        }

        if (student.calculateAverageGrades() < worstStudent.calculateAverageGrades()) {
            worstStudent = student; //upd worst
        }
    });

    console.log("\n=================[Best Student]================");
    bestStudent.printStudentInfo();
    console.log("\n=================[Worst Student]===============");
    worstStudent.printStudentInfo();
}

findBestAndWorstStudent(students);

// Ex 5: Create a list of students ordered by their grade average from best to worst
function orderStudentsByAverage(students) {
    students.sort((a, b) => b.calculateAverageGrades() - a.calculateAverageGrades());

    console.log("\n========[Students ordered by average grade]=========");
    students.forEach(student => student.printStudentInfo());
    console.log("===================================================");
}

orderStudentsByAverage(students);

// Ex 6: Filter Erasmus
function filterErasmusStudents(students) {
    const erasmusStudents = students.filter(student => student.isErasmus);

    console.log("\n================[Erasmus Students]================");
    erasmusStudents.forEach(student => student.printStudentInfo());
    console.log("==================================================");
}

filterErasmusStudents(students);
