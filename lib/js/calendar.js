function setup_calendar() {
    // fullCalendar semble avoir des problèmes avec jquery, une histoire de "double appel";

    let calendar_node = document.getElementById('calendar');
    let calendar = new FullCalendar.Calendar(calendar_node, {
        initialView: 'dayGridMonth',
        navLinks: true,
        headerToolbar: {
            left: 'prev,next,today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,listWeek'
        }
    });
    calendar.render();
}