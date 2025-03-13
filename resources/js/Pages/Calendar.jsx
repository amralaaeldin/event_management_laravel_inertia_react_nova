import React, { useMemo, useState } from "react";
import { Head, router, usePage } from "@inertiajs/react";
import { Calendar as BigCalendar, momentLocalizer } from "react-big-calendar";
import moment from "moment";
import "react-big-calendar/lib/css/react-big-calendar.css";

const localizer = momentLocalizer(moment);

export default function Calendar({ events, filters }) {
  console.log(events, filters);

  const [view, setView] = useState(filters.view || "week"); // Track current view
  const [currentDate, setCurrentDate] = useState(new Date(filters.date || new Date())); // Track selected date

  // Convert Laravel events to BigCalendar format
  const calendarEvents = useMemo(
    () =>
      events.map((event) => ({
        id: event.id,
        title: event.name,
        start: new Date(event.start_date_time),
        end: new Date(
          new Date(event.start_date_time).getTime() + event.duration * 60000
        ), // Convert duration from minutes to milliseconds
        is_attending: event.is_attending,
      })),
    [events]
  );

  const eventPropGetter = (event) => {
    let backgroundColor = "#3174ad";
    if (event.is_attending) backgroundColor = "#ff9800";
    return { style: { backgroundColor, color: "white" } };
  };

  // Handle view change (Month, Week, Day)
  const handleViewChange = (newView) => {
    setView(newView);
    fetchEvents(currentDate, newView);
  };

  // Handle navigation (Today, Back, Next)
  const handleNavigate = (newDate) => {
    setCurrentDate(newDate);
    fetchEvents(newDate, view);
  };

  // Fetch events using Inertia
  const fetchEvents = (date, viewType) => {
    router.get(
      route("calendar.index"),
      { date: moment(date).format("YYYY-MM-DD"), view: viewType },
      { preserveState: true, replace: true } // Preserve state for smooth updates
    );
  };

  return (
    <div className="container p-4 mx-auto">
      <Head title="Event Calendar" />
      <h1 className="mb-4 text-xl font-bold">Event Calendar</h1>
      <div className="h-screen">
        <BigCalendar
          localizer={localizer}
          events={calendarEvents}
          startAccessor="start"
          endAccessor="end"
          style={{ height: "80vh" }}
          eventPropGetter={eventPropGetter}
          views={["month", "week", "day"]}
          defaultView="week"
          onView={handleViewChange} // Track view changes
          onNavigate={handleNavigate} // Track navigation changes
        />
      </div>
    </div>
  );
}