function convertActionColumnToDropdown(tableSelector) {
  $(tableSelector + " tbody tr").each(function () {
    const $td = $(this).find("td:last");

    if ($td.find(".action-menu").length) return;
    if (!$td.find("a,button").length) return;

    const $items = $td.children("a,button").detach();

    const $menu = $(`
      <div class="dropdown action-menu">
        <button class="btn btn-sm" type="button" data-bs-toggle="dropdown" aria-expanded="false">
          <i class="bx bx-dots-vertical-rounded fs-5"></i>
        </button>
        <div class="dropdown-menu dropdown-menu-end"></div>
      </div>
    `);

    $items.each(function () {
      const $x = $(this);

      $x.removeClass(
        "btn btn-sm btn-warning btn-danger btn-primary btn-success btn-info btn-secondary",
      );

      $x.addClass("dropdown-item");

      const href = $x.attr("href") || "";
      const text = $.trim($x.text()).toLowerCase();

      if (href.includes("delete") || text.includes("delete")) {
        $x.addClass("text-danger");
      }

      $menu.find(".dropdown-menu").append($x);
    });

    $td.addClass("text-center").html($menu);
  });
}

function initAdminDataTable(
  tableSelector,
  moduleName = "records",
  hiddenColumns = [],
) {
  if (!window.jQuery || !$.fn.DataTable) {
    console.error("DataTable library not loaded.");
    return;
  }

  if (!$(tableSelector).length) return;

  if ($(tableSelector).data("action-dropdown") == 1) {
    convertActionColumnToDropdown(tableSelector);
  }

  if ($.fn.DataTable.isDataTable(tableSelector)) {
    $(tableSelector).DataTable().clear().destroy();
  }

  let actionColumnIndex = $(tableSelector + " thead th").length - 1;
  let notOrderable = [actionColumnIndex];

  if (Array.isArray(hiddenColumns) && hiddenColumns.length > 0) {
    notOrderable = notOrderable.concat(hiddenColumns);
  }

  let table = $(tableSelector).DataTable({
    scrollX: false,
    responsive: false,
    pageLength: 10,

    lengthMenu: [
      [10, 25, 50, 100, -1],
      [10, 25, 50, 100, "All"],
    ],

    dom:
      "<'dt-single-toolbar'<'dt-length-box'l><'dt-buttons-box'B><'dt-search-box'f>>" +
      "<'row'<'col-12'tr>>" +
      "<'dt-bottom-toolbar'<'dt-info-box'i><'dt-pagination-box'p>>",

    buttons: [
      {
        extend: "copyHtml5",
        text: '<i class="bx bx-copy"></i> Copy',
        className: "btn btn-light btn-sm",
        exportOptions: {
          columns: ":visible:not(:last-child)",
        },
      },
      {
        extend: "csvHtml5",
        text: '<i class="bx bx-file"></i> CSV',
        className: "btn btn-light btn-sm",
        exportOptions: {
          columns: ":visible:not(:last-child)",
        },
      },
      {
        extend: "excelHtml5",
        text: '<i class="bx bx-spreadsheet"></i> Excel',
        className: "btn btn-light btn-sm",
        exportOptions: {
          columns: ":visible:not(:last-child)",
        },
      },
      {
        extend: "pdfHtml5",
        text: '<i class="bx bxs-file-pdf"></i> PDF',
        className: "btn btn-light btn-sm",
        orientation: "landscape",
        pageSize: "A4",
        exportOptions: {
          columns: ":visible:not(:last-child)",
        },
      },
      {
        extend: "print",
        text: '<i class="bx bx-printer"></i> Print',
        className: "btn btn-light btn-sm",
        exportOptions: {
          columns: ":visible:not(:last-child)",
        },
      },
      {
        extend: "colvis",
        text: '<i class="bx bx-columns"></i> Columns',
        className: "btn btn-light btn-sm",
      },
    ],

    columnDefs: [
      {
        targets: notOrderable,
        orderable: false,
        searchable: false,
      },
    ],

    language: {
      search: "Search:",
      lengthMenu: "Show _MENU_ entries",
      info: "Showing _START_ to _END_ of _TOTAL_ " + moduleName,
      infoEmpty: "No " + moduleName + " available",
      zeroRecords: "No matching " + moduleName + " found",
    },
  });

  setTimeout(function () {
    table.columns.adjust().draw(false);
  }, 300);

  return table;
}

$(document).ready(function () {
  $(".datatable").each(function () {
    let tableId = $(this).attr("id");

    if (!tableId) {
      tableId = "adminTable_" + Math.floor(Math.random() * 999999);
      $(this).attr("id", tableId);
    }

    const moduleName = $(this).data("module") || "records";
    const hiddenCols = $(this).data("disable-columns") || "";

    let disableColumns = [];

    if (hiddenCols !== "") {
      disableColumns = hiddenCols
        .toString()
        .split(",")
        .map(function (item) {
          return parseInt(item.trim());
        })
        .filter(function (item) {
          return !isNaN(item);
        });
    }

    initAdminDataTable("#" + tableId, moduleName, disableColumns);
  });

  const current = location.pathname.split("/").pop();

  $('.menu-link[href="' + current + '"]')
    .closest(".menu-item")
    .addClass("active")
    .parents(".menu-item")
    .addClass("open active");
});
