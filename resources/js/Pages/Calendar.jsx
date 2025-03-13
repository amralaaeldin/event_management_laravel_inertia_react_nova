import React, { useMemo, useState, useRef } from "react";
import { Head, router, usePage } from "@inertiajs/react";
import EventModal from "@/Components/EventModal";
import { Calendar as BigCalendar, momentLocalizer } from "react-big-calendar";
import { ATTENDING_COLOR, WISHLIST_COLOR, EVENT_COLOR } from "../constants/colors";
import moment from "moment";
import "react-big-calendar/lib/css/react-big-calendar.css";

const localizer = momentLocalizer(moment);

export default function Calendar({ events, filters }) {
  const [view, setView] = useState(filters.view || "week"); // Track current view (Month, Week, Day)
  const [currentDate, setCurrentDate] = useState(new Date(filters.date || new Date())); // Track selected date - navigation (Today, Back, Next)
  const [hoveredEvent, setHoveredEvent] = useState(null);
  const hoverTimeout = useRef(null);
  const { flash } = usePage().props;

  // Convert Laravel events to BigCalendar format
  const calendarEvents = useMemo(
    () =>
      events.map((event) => ({
        id: event.id,
        title: event.name,
        description: event.description,
        start: new Date(event.start_date_time),
        end: new Date(
          new Date(event.start_date_time).getTime() + event.duration * 60000
        ), // Convert duration from minutes to milliseconds
        is_attending: event.is_attending,
        is_wishlist: event.is_wishlist,
      })),
    [events]
  );

  const eventPropGetter = (event) => {
    const backgroundColor = event.is_attending ? ATTENDING_COLOR : event.is_wishlist ? WISHLIST_COLOR : EVENT_COLOR; // Default color
    return { style: { backgroundColor, color: "white" } };
  };

  const handleViewChange = (newView) => {
    setView(newView);
    fetchEvents(currentDate, newView);
  };

  const handleNavigate = (newDate) => {
    setCurrentDate(newDate);
    fetchEvents(newDate, view);
  };

  const fetchEvents = (date, viewType) => {
    router.get(
      route("calendar.index"),
      { date: moment(date).format("YYYY-MM-DD"), view: viewType },
      { preserveState: true, replace: true } // Preserve state for smooth updates
    );
  };

  const handleEventClick = (event) => {
    const messageText = event.is_attending || event.is_wishlist ? 'unattend' : 'attend';
    if (confirm(`Do you want to ${messageText} ${event.title}?`)) {
      if (event.is_attending || event.is_wishlist) router.post(route("events.unattend", event.id));
      else router.post(route("events.attend", event.id));
    }
    setHoveredEvent(null);
  };

  const handleMouseEnter = (event) => {
    hoverTimeout.current = setTimeout(() => {
      setHoveredEvent(event);
    }, 500);
  };

  const handleMouseLeave = () => {
    clearTimeout(hoverTimeout.current); // Cancel timeout if user leaves early
    setHoveredEvent(null);
  };

  const { timezone, formattedOffset } = useMemo(() => {
    const offsetMinutes = new Date().getTimezoneOffset();
    const offsetHours = Math.abs(offsetMinutes / 60);
    const sign = offsetMinutes > 0 ? "-" : "+";
    const formattedOffset = `UTC ${sign}${String(offsetHours).padStart(2, "0")}:${String(offsetMinutes % 60).padStart(2, "0")}`;

    const timezone = Intl.DateTimeFormat().resolvedOptions().timeZone;

    return { timezone, formattedOffset };
  }, []);

  return (
    <div className="container p-4 mx-auto">
      {flash?.success && (
        <div className="absolute w-1/2 p-2 mb-4 text-green-800 bg-green-100 rounded right-10">
          {flash.success}
        </div>
      )}
      {flash?.error && (
        <div className="absolute w-1/2 p-2 mb-4 text-red-800 bg-red-100 rounded right-10">
          {flash.error}
        </div>
      )}

      <Head title="Event Calendar" />
      <div className="flex items-center gap-4 mb-4">
        <button onClick={() => window.history.back()} className="px-4 py-2 text-white bg-gray-500 rounded">
          ← Back
        </button>
        <h1 className="text-xl font-bold">Event Calendar</h1>
      </div>

      <div className="flex items-center gap-3 mb-4 ml-auto">
        <div className="flex items-center gap-2">
          <span className="w-4 h-4 rounded" style={{ backgroundColor: ATTENDING_COLOR }}></span>
          <span className="text-sm text-gray-700">Attending</span>
        </div>
        <div className="flex items-center gap-2">
          <span className="w-4 h-4 rounded" style={{ backgroundColor: WISHLIST_COLOR }}></span>
          <span className="text-sm text-gray-700">Waiting List</span>
        </div>
        <div className="flex items-center gap-2">
          <span className="w-4 h-4 rounded" style={{ backgroundColor: EVENT_COLOR }}></span>
          <span className="text-sm text-gray-700">Event</span>
        </div>
      </div>

      <div className="mb-4 ml-auto">
        <p className="text-sm text-gray-700">
          (•) Click on an event to toggle your attendance status.
        </p>
        <p className="text-sm text-gray-700">
          (•) Hover on an Event `title` to view details.
        </p>
        <p className="text-sm text-gray-700">
          (•) Times are according to your browser timezone. it's currently{' '}
          <span className="font-bold">
            {timezone} ({formattedOffset}).
          </span>
        </p>
      </div>

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
          onView={handleViewChange}
          onNavigate={handleNavigate}
          onSelectEvent={handleEventClick}
          components={{
            event: ({ event }) => (
              <div
                onMouseEnter={() => handleMouseEnter(event)}
                onMouseLeave={handleMouseLeave}
              >
                {event.title}
              </div>)
          }}
        />
      </div>

      <EventModal event={hoveredEvent} onClose={() => setHoveredEvent(null)} onJoin={() => handleEventClick(hoveredEvent)} />
    </div>
  );
}