import React from "react";
import { motion, AnimatePresence } from "framer-motion";
import { ATTENDING_COLOR, WISHLIST_COLOR, EVENT_COLOR } from "../constants/colors";

export default function EventModal({ event, onClose }) {
  if (!event) return null;

  const getBackgroundColor = () => {
    if (event.is_attending) return ATTENDING_COLOR;
    if (event.is_wishlist) return WISHLIST_COLOR;
    return EVENT_COLOR;
  };

  return (
    <AnimatePresence>
      <motion.div
        className="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50"
        initial={{ opacity: 0 }} // Start invisible
        animate={{ opacity: 1 }} // Fade in
        exit={{ opacity: 0 }} // Fade out
      >
        <motion.div
          className="w-full max-w-sm p-6 text-white rounded-lg shadow-lg"
          style={{ backgroundColor: getBackgroundColor() }}
          initial={{ scale: 0.8, opacity: 0 }} // Start small & transparent
          animate={{ scale: 1, opacity: 1 }} // Grow to full size
          exit={{ scale: 0.8, opacity: 0 }} // Shrink on close
          transition={{ duration: 0.3, ease: "easeOut" }} // Smooth animation
        >
          <h2 className="text-2xl font-bold text-gray-100">{event.title}</h2>

          <div className="flex items-center gap-2 mt-2 text-sm text-gray-300">
            <span className="font-semibold">📅 Start:</span> {event.start.toLocaleString()}
          </div>
          <div className="flex items-center gap-2 text-sm text-gray-300">
            <span className="font-semibold">⏳ End:</span> {event.end.toLocaleString()}
          </div>

          <div className="flex items-center gap-2 mt-3">
            <span className="font-semibold text-gray-200">📌 Status:</span>
            <span
              className={`px-2 py-1 text-xs font-semibold rounded-lg ${event.is_attending
                ? "bg-yellow-500 text-black"
                : event.is_wishlist
                  ? "bg-green-600"
                  : "bg-blue-500"
                }`}
            >
              {event.is_attending
                ? "Attending"
                : event.is_wishlist
                  ? "Wishlist"
                  : "General Event"}
            </span>
          </div>

          {event.description && (
            <div className="pt-3 mt-3 text-gray-200 border-t border-gray-700">
              <h3 className="text-xl font-bold text-gray-100">Description</h3>
              <p className="text-sm">
                {event.description}
              </p>
            </div>
          )}

          <div className="flex justify-end mt-4">
            <button
              onClick={onClose}
              className="px-4 py-2 transition bg-gray-900 rounded-lg hover:bg-gray-700"
            >
              Close
            </button>
          </div>
        </motion.div>
      </motion.div>
    </AnimatePresence>
  );
}
