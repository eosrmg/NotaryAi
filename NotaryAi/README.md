# NotaryAi

NotaryAi is a web application designed to provide a calculator for various financial computations, including currency conversions and tax calculations. This project is built using Vue.js and TypeScript, leveraging modern web development practices.

## Project Structure

The project is organized as follows:

```
NotaryAi
├── resources
│   ├── js
│   │   ├── components
│   │   │   ├── ui
│   │   │   │   ├── button.vue        # Vue component for buttons
│   │   │   │   ├── calendar.vue      # Vue component for calendar
│   │   │   │   ├── dialog.vue        # Vue component for dialogs/modals
│   │   │   │   ├── input.vue         # Vue component for input fields
│   │   │   │   └── switch.vue        # Vue component for toggle switches
│   │   │   └── index.vue             # Index file for exporting UI components
│   │   ├── layouts
│   │   │   └── AppLayout.vue         # Main layout component for the application
│   │   ├── pages
│   │   │   └── Calculator.vue        # Calculator page component
│   │   └── types
│   │       └── index.ts              # TypeScript types and interfaces
│   └── css
│       └── styles.css                # Global styles for the application
├── public
│   └── index.html                    # Main HTML file for the application
├── package.json                      # npm configuration file
├── tsconfig.json                    # TypeScript configuration file
├── vite.config.ts                   # Vite configuration file
└── README.md                        # Project documentation
```

## Installation

To get started with NotaryAi, follow these steps:

1. Clone the repository:
   ```
   git clone <repository-url>
   ```

2. Navigate to the project directory:
   ```
   cd NotaryAi
   ```

3. Install the dependencies:
   ```
   npm install
   ```

## Usage

To run the application in development mode, use the following command:

```
npm run dev
```

This will start the Vite development server, and you can access the application at `http://localhost:3000`.

## Contributing

Contributions are welcome! If you have suggestions for improvements or new features, feel free to open an issue or submit a pull request.

## License

This project is licensed under the MIT License. See the LICENSE file for more details.