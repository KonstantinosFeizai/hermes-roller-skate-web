// table-labels.js
// Purpose: Add data-label attributes to table cells for responsive layouts.
document.addEventListener("DOMContentLoaded", () => {
  // Process each table that uses the responsive table layout
  document.querySelectorAll(".user-table").forEach((table) => {
    // Collect header text (cleaned) from the table head
    const headers = Array.from(table.querySelectorAll("thead th")).map((th) =>
      th.textContent.replace(/\s+/g, " ").trim(),
    );
    if (!headers.length) return;

    // Add data-label to each cell using the corresponding header text
    table.querySelectorAll("tbody tr").forEach((row) => {
      Array.from(row.children).forEach((cell, index) => {
        if (!cell.hasAttribute("data-label") && headers[index]) {
          cell.setAttribute("data-label", headers[index]);
        }
      });
    });
  });
});
