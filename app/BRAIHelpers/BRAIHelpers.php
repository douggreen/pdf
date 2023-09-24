<?php

class BRAIHelpers
{

  const MAX_COLUMNS = 3;

  const MAX_CHARACTERS_PER_LINE = 37;

  /**
   * The maximum number of lines available in a single column.
   *
   * @var int
   */
  protected $linesPerColumn;

  /**
   * The number of lines used by the Day header.
   *
   * @var int
   */
  protected $linesPerDayHeader;

  /**
   * @param int $page_height
   * @param int $line_height;
   */
  public function __construct($page_height, $line_height)
  {
    // @todo: actual height of each h1, h3, body, etc...
    $this->linesPerColumn = floor(0.65 * $page_height / $line_height);

    // This is an estimate that takes into account the day header size and
    // padding. @todo: make this exact.
    $this->linesPerDayHeader = 3;
  }

  // determine the number of rows the meeting text will occupy and add that to the number of lines in the column
  public function getNumMeetingLines(mixed $meeting) : int
  {
    // Create a simulation of the output.
    $meeting_parts = [$meeting->name];
    if (!empty($meeting->location) && $meeting->location != $meeting->name) {
      $meeting_parts[] = $meeting->location;
    }
    $meeting_parts[] = $meeting->address . ' (' . implode(', ', $meeting->types) . ')';
    $output = implode('|', $meeting_parts);

    // Wrap the simulated output to count the number of lines.
    $wrapped_meeting_string = wordwrap($output, self::MAX_CHARACTERS_PER_LINE, '|');

    // Return the number of lines.
    // 1. add one for the date - region, and
    // 2. add one because the count of '|' doesn't account for the last string.
    return substr_count($wrapped_meeting_string, '|') + 2;
  }

  /**
   * @param int $row
   *   The current row.
   * @param int $column
   *   The current column.
   * @param int $position
   *   The current column line position.
   * @param string $day
   *   The current day.
   */
  public function next(int& $row, int& $column, int& $position, string $day = ''): void
  {
    $next_position = $position;
    if ($day) {
      $next_position += $this->linesPerDayHeader;
    }

    if ($next_position > $this->linesPerColumn) {
      if ($column >= self::MAX_COLUMNS) {
        print $this->newRow();
        if (!empty($day)) {
          print $this->newDay($day);
        }
        $row++;
        $column = 1;
        $position = $this->linesPerDayHeader;
      }
      else {
        print $this->newColumn();
        $column++;
        $position = 0;
      }
    }
  }

  /**
   * Returns the HTML to start a new day.
   *
   * @param string $day
   *
   * @return string
   */
  public function newDay(string $day) : string
  {
    return '<h1 class="brai-day">' . strtoupper($day) . '</h1>';
  }

  /**
   * Returns the HTML to start a new row.
   *
   * @return string
   */
  protected function newRow() : string
  {
    return '</div></div><div class="row"><div class="column">';
  }

  /**
   * Returns the HTML to start a new column.
   *
   * @return string
   */
  protected function newColumn() : string
  {
    return '</div><div class="column">';
  }

}
